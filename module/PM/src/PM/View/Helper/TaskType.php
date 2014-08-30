<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/TaskType.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Tasks;

 /**
 * PM - Task Type View Helper
 *
 * @package 	ViewHelpers\Tasks
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/View/Helper/TaskType.php
 */
class TaskType extends BaseViewHelper
{
	public function __invoke($type)
	{
		
		return 'vv';
	    $helperPluginManager = $this->getServiceLocator();
	    $serviceManager = $helperPluginManager->getServiceLocator();
	    
	    $options = $serviceManager->get('PM\Model\Options');
		return Tasks::translateTypeId($type, $options); 
	}
}