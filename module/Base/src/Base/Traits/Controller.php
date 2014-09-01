<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Traits/Controller.php
 */

namespace Base\Traits;

/**
 * Base - Controller Trait
 *
 * Contains the global goodies for the Base module and others
 *
 * @package 	MojiTrac\Traits
 * @author		Eric Lamb
 * @filesource 	./module/Base/src/Base/Traits/Controller.php
 */
trait Controller 
{
	/**
	 * The database adapter connection
	 * @var \Zend\Db\Adapter\Adapter
	 */
	protected $adapter;
	
	/**
	 * The actual SQL object for making queries with
	 * @var object
	 */
	protected $db;
	
	protected $authservice;
	
	protected $storage;
	
	public function getAuthService()
	{
		if (! $this->authservice ) {
			$this->authservice = $this->getServiceLocator()->get('AuthService');
		}
	
		return $this->authservice;
	}
	
	public function getSessionStorage()
	{
		if (! $this->storage) {
			$this->storage = $this->getServiceLocator()->get('Application\Model\Auth\AuthStorage');
		}
	
		return $this->storage;
	}
	
	public function getAdapter()
	{
		if (!$this->adapter) {
			$sm = $this->getServiceLocator();
			$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
		}
		return $this->adapter;
	}	
	
	public function translate($lang, $domain = 'app')
	{
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		return $translate($lang, $domain);
	}
	
	public function downloadFile($file)
	{
		
		echo $file;
		exit;
		$response = new \Zend\Http\Response\Stream();
		$response->setStream(fopen($file, 'r'));
		$response->setStatusCode(200);
		
		$headers = new \Zend\Http\Headers();
		$headers->addHeaderLine('Content-Type', 'whatever your content type is')
		->addHeaderLine('Content-Disposition', 'attachment; filename="' . $fileName . '"')
		->addHeaderLine('Content-Length', filesize($fileName));
		
		$response->setHeaders($headers);
		return $response;		
	}
}