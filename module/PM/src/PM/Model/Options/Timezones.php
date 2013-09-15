<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Timezones.php
 */

namespace PM\Model\Options;

use DateTimeZone;

/**
 * PM - Timezones Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
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