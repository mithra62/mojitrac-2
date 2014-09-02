<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Languages.php
 */

namespace PM\Model\Options;

use DateTimeZone;

/**
 * PM - Languages Options Model
 *
 * @package 	Localization\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Languages.php
 */
class Languages
{
	static public function langs()
	{
	    $return = array('en_US' => 'English / US', 'es_ES' => 'Spanish');
		return $return;
	}
}