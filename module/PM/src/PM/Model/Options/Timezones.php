<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Timezones.php
 */

namespace PM\Model\Options;

use DateTimeZone;

/**
 * PM - Timezones Options Model
 *
 * @package 	Localization\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Timezones.php
 */
class Timezones
{
	static public function tz()
	{
		$tz = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
		$return = array();
		
		foreach($tz AS $key => $value)
		{
		    $return[$value] = $value;
		}
		return $return;
	}
}