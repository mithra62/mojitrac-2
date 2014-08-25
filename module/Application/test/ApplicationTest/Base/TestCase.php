<?php
namespace ApplicationTest\Base;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

abstract class TestCase extends AbstractHttpControllerTestCase
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
		$this->serviceManager = $this->getApplicationServiceLocator();
	}	
}