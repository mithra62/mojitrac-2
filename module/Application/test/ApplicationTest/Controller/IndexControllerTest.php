<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/test/ApplicationTest/Controller/ApplicationControllerTest.php
 */

namespace ApplicationTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Application - Index Test Controller
 *
 * Tests the IndexController functionality
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/test/ApplicationTest/Controller/IndexControllerTest.php
 */
class IndexControllerTest extends AbstractHttpControllerTestCase
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
     * Verifies the index action behaves proper
     */
    public function testIndexActionCanBeAccessed()
    {
    	$this->dispatch('/');
    	$this->assertResponseStatusCode(302);    
    	$this->assertModuleName('Application');
    	$this->assertControllerName('Application\Controller\Index');
    	$this->assertControllerClass('IndexController');
    	$this->assertActionName('index');
    	$this->assertMatchedRouteName('home');
    	$this->assertRedirectTo('/login');
    }   
}