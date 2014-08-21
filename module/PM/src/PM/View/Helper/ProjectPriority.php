<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectPriority.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;
use PM\Model\Options\Projects;

 /**
 * PM - Project Priority View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/ProjectPriority.php
 */
class ProjectPriority extends BaseViewHelper
{
	public function __invoke($priority)
	{
		$return = Projects::translatePriorityId($priority); 
		//$url = $this->view->StaticUrl();
		$return = '<img src="'.$this->view->serverUrl($url.'/images/priorities/'.$priority.'.gif.').'" alt="'.$return.'" title="'.$return.'" /> '.$return;
		return $return; 
	}
}