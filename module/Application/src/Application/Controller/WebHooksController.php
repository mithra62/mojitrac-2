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
class WebHooksController extends Default_Abstract
{
	
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->initView();
    }

    public function indexAction()
    {
    	echo 'fsda';
    	exit;
    } 

    public function beanstalkAction()
    {
    	//check if webhooks for beanstalk are enabled
    	
    	//validate project id
    	echo $company_id = $this->_request->getParam('id', FALSE);
    	
    	//ensure webhooks are enabled for project
    	
    	//determine type of repo beanstalk is using (git vs svn)
    	
    	//validate webhooks
    	
    	//save webhook data
    	echo __METHOD__;
    	exit;
    }
    
    public function consumerAction()
    {
    	
    }
    
    public function callbackAction()
    {
    	
    }
}