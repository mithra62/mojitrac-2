<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/TaskPriority.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;
use Application\View\Helper\AbstractViewHelper;
use PM\Model\Options\Projects;

 /**
 * PM - Task Priority View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/TaskPriority.php
 */
class TaskPriority extends AbstractViewHelper
{   	
	function __invoke($priority)
	{
		$return = Projects::translatePriorityId($priority); 
		$return = '<img src="'.$this->view->StaticUrl().'/images/priorities/'.$priority.'.gif" alt="'.$return.'" title="'.$return.'" /> '.$return;
		return $return;
	}
}