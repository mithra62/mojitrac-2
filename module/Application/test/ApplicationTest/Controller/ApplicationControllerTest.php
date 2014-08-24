<?php

namespace ApplicationTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ApplicationControllerTest extends AbstractHttpControllerTestCase
{
	protected $traceError = true;
	
    public function setUp()
    {
        $this->setApplicationConfig(
            include 'D:\ProjectFiles\mithra62\moji2\config/application.config.php'
        );
        parent::setUp();
    }
    
    public function testIndexActionCanBeAccessed()
    {
    	$this->dispatch('/');
    	$this->assertResponseStatusCode(302);    
    	$this->assertModuleName('Application');
    	$this->assertControllerName('Application\Controller\Index');
    	$this->assertControllerClass('IndexController');
    	$this->assertMatchedRouteName('home');
    }    
}