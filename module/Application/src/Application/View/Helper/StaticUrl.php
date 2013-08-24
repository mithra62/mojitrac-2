<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./module/Application/view//helper/StaticUrl.php
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;
use Application\View\Helper\AbstractViewHelper;

 /**
 * Default - Abstract Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/view//helper/StaticUrl.php
 */
class Staticurl extends AbstractViewHelper
{
	public function __invoke()
	{

	}
}