<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/IsDatePast.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Is Date Past View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View/Helper/IsDatePast.php
 */
class IsDatePast extends BaseViewHelper
{
	public function __invoke($date)
	{
		$d = strtotime($date);
		if($d && $d < time())
		{
			return TRUE;
		}
	}
}