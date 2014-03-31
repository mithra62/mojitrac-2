<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Languages.php
 */

namespace PM\Model\Options;

use DateTimeZone;

/**
 * PM - Languages Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
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