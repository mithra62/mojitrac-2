<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/controllers/LoginController.php
 */

/**
 * Include the AbstractController Controller class
 */
include 'AbstractController.php';

 /**
 * Default - Login Class
 *
 * Handles login routing 
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/LoginController.php
 */
class OauthController extends Default_Abstract
{
	
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();
    }

    public function indexAction()
    {       	
    	$config = array(
    			'callbackUrl' => 'http://eric.moji.com/oauth/callback/',
    			'siteUrl' => 'http://github.com/login/oauth/authorize/'
    	);
    	
    	$consumer = new ZendOAuth\Consumer($config);	
    	
    	// fetch a request token
    	$token = $consumer->getRequestToken();
    	
    	$_SESSION['GITHUB_REQUEST_TOKEN'] = serialize($token);

		// redirect the user
		$consumer->redirect();
    }  
    
    public function consumerAction()
    {
    	
    }
    
    public function callbackAction()
    {
    	
    }
}