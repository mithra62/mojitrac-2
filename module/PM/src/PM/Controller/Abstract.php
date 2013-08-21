<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/Abstract.php
*/

/**
* PM - Controller Abstract
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/Abstract.php
*/
abstract class PM_Abstract extends Zend_Controller_Action
{
	/**
	 * Flash Messenger
	 * @var string
	 */
	protected $_flashMessenger = null;

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
	
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
		if($this->_request->isXmlHttpRequest())
		{
    		$this->view->layout()->disableLayout();
    		$this->view->ajax_mode = TRUE;
		}		
	}	
	
	/**
	 * Compiles the class variables
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
    	
    	if(LambLib_Controller_Action_Helper_Utilities::moji_installed() === FALSE)
    	{
    		$this->_helper->redirector('index', 'index', 'install');
    		exit;    		
    	}
    	
		$this->session = new Zend_Session_Namespace('PM');
		$this->view->uri = $this->_request->getPathInfo();

		$settings = new Model_Settings;
		$this->settings = $settings->getSettings();	
		Zend_Registry::set('pm_settings', $this->settings);
		
        /* Initialize action controller here */
    	if (!Zend_Auth::getInstance()->hasIdentity()) 
    	{
    		$this->session->redirect_to = $this->view->uri;
    		$this->session->setExpirationSeconds(120, 'redirect_to');
            $this->_helper->redirector('index', 'login', 'default');
            exit;
        }

        $this->initView();
	    if($this->_request->isXmlHttpRequest())
		{
    		$this->view->layout()->disableLayout();
    		$this->view->ajax_mode = TRUE;
		}	
		
		$this->identity = Zend_Auth::getInstance()->getIdentity(); 
		
		$this->_initPrefs();
		$this->prefs = Zend_Registry::get('pm_prefs');
        $this->perm = new PM_Model_Permissions;
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger'); 
        $this->view->messages = $this->_flashMessenger->getMessages(); 
        $this->view->layout_style = 'left';
        $this->view->sidebar = 'dashboard';
        $this->view->active_nav = 'home';
        $this->view->sub_menu = 'dashboard';
        $this->_initIpBlocker();
    }

    /**
     * Provides oversight on permission dependant requsts
     * @param string $permission
     * @param string $url
     */
    public function check_permission($permission, $url = FALSE)
    {
        if(!$this->perm->check($this->identity, $permission))
        {
        	if(!$url)
        	{
        		$this->_helper->redirector('index', 'index', 'pm');
        	}
        	
        	exit;
        }     	
    }
    
    /**
     * Start up the IP Blocker
     */
    protected function _initIpBlocker()
    {
    	if(array_key_exists('enable_ip', $this->settings) && $this->settings['enable_ip'] !== FALSE)
    	{
    		$ip = new PM_Model_Ips;
    		if(!$ip->isAllowed($_SERVER['REMOTE_ADDR']))
    		{
    			exit;
    		}
    	}
    } 

    /**
     * Start up the preferences and settings overrides
     */
    protected function _initPrefs()
    {
    	$ud = new PM_Model_User_Data;
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

    	Zend_Registry::set('pm_prefs', $this->prefs);
    }    
		
}