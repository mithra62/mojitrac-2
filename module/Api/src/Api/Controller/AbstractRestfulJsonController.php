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
			return new ApiProblemResponse(new ApiProblem(401, 'Authorization Required!'));
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
			return new ApiProblemResponse(new ApiProblem(401, 'Unallowed IP Address!'));
		}
		
		$this->_initEvents();
	
		return parent::onDispatch( $e );
	}
	
	private function _initEvents()
	{
		//setup the Activity Log
		$al = $this->getServiceLocator()->get('PM\Event\ActivityLogEvent');
		$al->register($this->getEventManager()->getSharedManager());
	
		$al = $this->getServiceLocator()->get('PM\Event\NotificationEvent');
		$al->register($this->getEventManager()->getSharedManager());
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
			return new ApiProblemResponse(new ApiProblem(401, 'Authorization Required!'));
		}
			
		if(!$this->perm->check($this->identity, $permission))
		{
			return new ApiProblemResponse(new ApiProblem(401, 'You aren\'t allowed to perform this request!'));
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
		
    protected function methodNotAllowed()
    {
        $this->response->setStatusCode(405);
        throw new \Exception('Method Not Allowed');
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

    public function options()
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