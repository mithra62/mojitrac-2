<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Api/src/Api/Controller/AbstractRestfulJsonController.php
*/

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\EventManager\EventManagerInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Api - Abstract Controller
 *
 * Sets all the global functionality up for the REST API
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Controller/AbstractRestfulJsonController.php
 */
class AbstractRestfulJsonController extends AbstractRestfulController
{
	/**
	 * Session
	 * @var object
	 */
	protected $session;
	
	/**
	 * Permission Object
	 * @var object
	 */
	protected $perm;
	
	/**
	 * Settings array
	 * @var array
	 */
	protected $settings;
	
	/**
	 * Preferences array
	 * @var array
	 */
	protected $prefs;
	
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if( empty($this->identity) )
		{
			return $this->setError(401, 'Authorization Required!');
		}
	
		$settings = $this->getServiceLocator()->get('Application\Model\Settings');
		$this->settings = $settings->getSettings();
	
		$this->getServiceLocator()->get('Timezone');
	
		$this->_initPrefs();
		$this->perm = $this->getServiceLocator()->get('Application\Model\Permissions');
	
		$translator = $e->getApplication()->getServiceManager()->get('translator');
		$translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']))->setFallbackLocale('en_US');

		if( !$this->_initIpBlocker() )
		{
			return $this->setError(401, 'Unallowed IP Address!');
		}
		
		$this->_initEvents();
	
		return parent::onDispatch( $e );
	}
	
	public function setError($code, $detail, $type = null, $title = null, array $additional = array())
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		return new ApiProblemResponse(new ApiProblem($code, $translate($detail, 'api'), $type, $title, $additional));
	}
	
	private function _initEvents()
	{
		//setup the Activity Log
		$al = $this->getServiceLocator()->get('PM\Event\ActivityLogEvent');
		$al->register($this->getEventManager()->getSharedManager());
	
		$al = $this->getServiceLocator()->get('PM\Event\NotificationEvent');
		$al->register($this->getEventManager()->getSharedManager());
	}
	
	public function setEventManager(EventManagerInterface $events)
	{
		parent::setEventManager($events);
		$this->events = $events;
		$events->attach('dispatch', array($this, 'checkOptions'), 10);
	}
	
	/**
	 * Provides oversight on permission dependant requsts
	 * @param string $permission
	 * @param string $url
	 */
	public function check_permission($permission, $url = FALSE)
	{
		$this->identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
		if( empty($this->identity) )
		{
			return FALSE;
		}
			
		if(!$this->perm->check($this->identity, $permission))
		{
			return FALSE;
		}
	}
	
	/**
	 * Start up the IP Blocker
	 */
	protected function _initIpBlocker()
	{
		if(!empty($this->settings['enable_ip']) && $this->settings['enable_ip'] == '1')
		{
			$ip = $this->getServiceLocator()->get('PM\Model\Ips');
			if(!$ip->isAllowed($_SERVER['REMOTE_ADDR']))
			{
				return FALSE;
			}
		}

		return TRUE;
	}
	
	/**
	 * Start up the preferences and settings overrides
	 */
	protected function _initPrefs()
	{
		$ud = $this->getServiceLocator()->get('Application\Model\User\Data');
		$this->prefs = $ud->getUsersData($this->identity);
		foreach($this->settings AS $key => $value)
		{
			if(isset($this->prefs[$key]) && $this->prefs[$key] != '')
			{
				$this->settings[$key] = $this->prefs[$key];
			}
			else
			{
				$this->prefs[$key] = $this->settings[$key];
			}
		}
	}
	
	public function options()
	{
		if($this->params()->fromRoute('id', false))
		{
			$options = $this->resourceOptions;
		}
		else
		{
			$options = $this->collectionOptions;
		}
	
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('Allow', implode(',', $options));
		return $response;
	}
	
	public function checkOptions($e)
	{
		if($this->params()->fromRoute('id', false))
		{
			$options = $this->resourceOptions;
		}
		else
		{
			$options = $this->collectionOptions;
		}
		 
		if(in_array($e->getRequest()->getMethod(), $options))
		{
			return;
		}
	
		$response = $this->getResponse();
		$response->setStatusCode(405);
		return $response;
	}	
	
	/**
	 * Sets the HTTP header code that's passed
	 * @param int $code
	 */
	public function setStatusCode($code)
	{
		$response = $this->getResponse();
		$response->setStatusCode($code);
	}
		
    protected function methodNotAllowed()
    {
    	return $this->setError(405, 'method_not_allowed');
    }

    # Override default actions as they do not return valid JsonModels
    public function create($data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }

    public function deleteList()
    {
        return $this->methodNotAllowed();
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function getList()
    {
        return $this->methodNotAllowed();
    }

    public function head($id = null)
    {
        return $this->methodNotAllowed();
    }   

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function replaceList($data)
    {
        return $this->methodNotAllowed();
    }

    public function patchList($data)
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }
}