<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/test/ApplicationTest/Model/LoginTest.php
 */

namespace ApplicationTest\Model;

use ApplicationTest\Base\TestCase;

/**
 * Application - Login Test Model
 *
 * Tests the Login Model methods
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/test/ApplicationTest/Model/LoginTest.php
 */
class LoginTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->login = $this->serviceManager->get('Application\Model\Login');
	}
	
	public function testAuthServiceInstance()
	{
		$serviceManager = $this->getApplicationServiceLocator();
		$adapter = $serviceManager->get('AuthService');
		$this->assertInstanceOf('Zend\Authentication\AuthenticationService', $adapter);
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('Application\Model\Login', $this->login);
	}
	
	public function testInputFilterInstance()
	{
		$this->login->setAuthAdapter($this->serviceManager->get('Zend\Db\Adapter\Adapter'));
		$this->assertInstanceOf('Zend\InputFilter\Inputfilter', $this->login->getInputFilter());
	}
	
	public function testProcLoginFail()
	{	
		$email = 'foo';
		$password = 'bar';
		$adapter = $this->serviceManager->get('AuthService');
		$result = $this->login->procLogin($email, $password, $adapter);
		$this->assertEquals(-1, $result);
	}
	
	public function testProcLoginSuccess()
	{
		$email = $this->credentials['email'];
		$password = $this->credentials['password'];
		$adapter = $this->serviceManager->get('AuthService');
		$result = $this->login->procLogin($email, $password, $adapter);
		$this->assertEquals(1, $result);	
	}
}