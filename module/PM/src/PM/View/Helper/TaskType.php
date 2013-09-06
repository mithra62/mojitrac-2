<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
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
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/TaskType.php
 */
class TaskType extends BaseViewHelper
{
	public function __invoke($type)
	{
	    $helperPluginManager = $this->getServiceLocator();
	    $serviceManager = $helperPluginManager->getServiceLocator();
	    
	    $options = $serviceManager->get('PM\Model\Options');
	    	    
		return Tasks::translateTypeId($type, $options); 
	}
}