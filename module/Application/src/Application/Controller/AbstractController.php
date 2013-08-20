<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/controllers/AbstractController.php
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Adapter\AuthAdapter;
use Zend\XmlRpc\Value\ArrayValue;

 /**
 * Default - AbstractController Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/AbstractController.php
 */
abstract class AbstractController extends AbstractActionController
{
	/**
	 * ZF Config
	 * Contains the entire compiled configuration 
	 * @var Array
	 */
	public $config = array();
	
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$this->config = $this->getServiceLocator()->get('Config');
		return parent::onDispatch( $e );
	}
}