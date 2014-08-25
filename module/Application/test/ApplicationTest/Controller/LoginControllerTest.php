<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/test/ApplicationTest/Controller/LoginControllerTest.php
 */

namespace ApplicationTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Application - Login Test Controller
 *
 * Tests the LoginController functionality
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/test/ApplicationTest/Controller/LoginControllerTest.php
 */
class LoginControllerTest extends AbstractHttpControllerTestCase
{
	/**
	 * Should errors be traced for output
	 * @var bool
	 */
	protected $traceError = true;
	
    public function setUp()
    {
        $this->setApplicationConfig(
            include 'D:\ProjectFiles\mithra62\moji2\config/application.config.php'
        );
        parent::setUp();
    }
    
    /**
     * Verifies the index action can be accessed
     */
    public function testLoginActionCanBeAccessed()
    {
    	$this->dispatch('/login');
    	$this->assertResponseStatusCode(200);    
    	$this->assertModuleName('Application');
    	$this->assertControllerName('Application\Controller\Login');
    	$this->assertControllerClass('LoginController');
    	$this->assertActionName('index');
    	$this->assertMatchedRouteName('login');
    } 

    /**
     * @depends testLoginActionCanBeAccessed
     */
    public function testLoginActionBadEmail()
    {
    	$params = array(
    		'email' => 'test',
    		'password' => 'fdsafdsa'
    	);
    	
    	$this->dispatch('/login', 'POST', $params);
    	$this->assertResponseStatusCode(200);
    	$this->assertNotRedirect();
    	//$this->assertXpathQueryContentContains('ul.errors li', 'not a valid');
    }
    
    public function testLoginModelInstance()
    {
    	$serviceManager = $this->getApplicationServiceLocator();
    	$login = $serviceManager->get('Application\Model\Login');
    	$this->assertInstanceOf('Application\Model\Login', $login);
    }
    
    public function testLoginModelInputFilterInstance()
    {
    	$serviceManager = $this->getApplicationServiceLocator();
    	$login = $serviceManager->get('Application\Model\Login');
    	$login->setAuthAdapter($serviceManager->get('Zend\Db\Adapter\Adapter'));
    	$this->assertInstanceOf('Zend\InputFilter\Inputfilter', $login->getInputFilter());
    }
}