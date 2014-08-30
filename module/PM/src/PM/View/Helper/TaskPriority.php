<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/TaskPriority.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Projects;

 /**
 * PM - Task Priority View Helper
 *
 * @package 	ViewHelpers\Tasks
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/TaskPriority.php
 */
class TaskPriority extends BaseViewHelper
{   	
	function __invoke($priority)
	{
		return 'ttt;';
		$return = Projects::translatePriorityId($priority); 
		$return = '<img src="'.$this->view->StaticUrl().'/images/priorities/'.$priority.'.gif" alt="'.$return.'" title="'.$return.'" /> '.$return;
		return $return;
	}
}