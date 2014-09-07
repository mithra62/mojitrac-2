<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options/Invoices.php
 */

namespace PM\Model\Options;

/**
 * PM - Invoices Options Model
 *
 * @package 	Invoices\Options
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Model/Options/Invoices.php
 */
class Invoices
{
	static public function status()
	{
		$types = array();
		$types[0] = 'Draft';
		$types[1] = 'Sent';		
		$types[2] = 'Paid';
		$types[3] = 'Cancelled';
		$types[4] = 'Refunded';
		
		return $types;
	}
	
	static public function types()
	{
		$types = array();
		$types[0] = 'Time Based';
		$types[1] = 'Item Based';		
		$types[2] = 'Combined';
		
		return $types;
	}

	static public function translateStatusId($id)
	{
		$types = self::status();
		return $types[$id];
	}
}