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

namespace ApplicationTest\Model;

use ApplicationTest\Base\TestCase;

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
	
	/**
	public function testProcLoginSuccess()
	{
		$email = '';
		$password = '';
		$adapter = $this->serviceManager->get('AuthService');
		$result = $this->login->procLogin($email, $password, $adapter);
		$this->assertEquals(1, $result);	
	}
	**/
}