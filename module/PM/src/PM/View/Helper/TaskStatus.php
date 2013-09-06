<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/TaskStatus.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Projects;

 /**
 * PM - Task Status View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/TaskStatus.php
 */
class TaskStatus extends BaseViewHelper
{
	function __invoke($status)
	{
		return Projects::translateStatusId($status); 
	}
}