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

use ApplicationTest\Base\TestCase;
use Zend\Dom\Query;

/**
 * Application - Login Test Controller
 *
 * Tests the LoginController functionality
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/test/ApplicationTest/Controller/LoginControllerTest.php
 */
class LoginControllerTest extends TestCase
{
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
    	$this->dispatch('/login');
    	$body = $this->getResponse()->getBody();
    	
    	$params = array(
    		'email' => 'test',
    		'password' => 'fdsafdsa',
    	);
    	
    	$this->dispatch('/login', 'POST', $params);
    	$this->assertResponseStatusCode(200);
    	$this->assertNotRedirect();
    	//$this->assertQueryContentContains('li.error[0]', 'not a valid');
    }  
    
    /**
     * @depends testLoginActionBadEmail
     */
    public function testLoginActionGoodCredentials()
    {
    	$this->dispatch('/login');
    	$html = $this->getResponse()->getBody();
    	$dom = new Query($html);
    	$csrf = $dom->execute('input[name="_x"]')->current()->getAttribute('value');
    	
    	$params = array(
    		'email' => $this->credentials['email'].'fff',
    		'password' => $this->credentials['password'],
    		'_x' => $csrf
    	);
    	
    	$this->reset();
    	$this->dispatch('/login', 'POST', $params);
    	$this->assertResponseStatusCode(200);
    	$this->assertNotRedirect();
    	//$this->assertXpathQueryContentContains('ul.errors li', 'not a valid');
    }
}