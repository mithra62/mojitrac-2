<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./moji/application/forms/Hash.php
*/

namespace Application\Model;

/**
* Hash Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/models/Hash.php
*/
class Hash
{
	protected $key = 'c6PKuC t(b]a))NIuM)eX>^82+4kvk!@!v(eXM{yGW9yLFC A0D0X;]8pQ0@Iv~|';
	
	public function __construct()
	{
		
	}
	
	public function gen_salt()
	{
		return md5(microtime());
	}
	
	public function password($password, $salt)
	{
		$salt = hash_hmac('sha512', $salt, $this->key);
		return hash_hmac('sha512', $password . $salt, $this->key);
	}
	
	public function guid()
	{
	    if (function_exists('com_create_guid'))
	    {
	        return com_create_guid();
	    }
	    else
	    {
	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true).$this->key));
	        $hyphen = chr(45);// "-"
	        $uuid = chr(123)// "{"
	                .substr($charid, 0, 8).$hyphen
	                .substr($charid, 8, 4).$hyphen
	                .substr($charid,12, 4).$hyphen
	                .substr($charid,16, 4).$hyphen
	                .substr($charid,20,12)
	                .chr(125);// "}"
	        return $uuid;
	    }		
	}
	
	public function guidish()
	{
		return strtolower(str_replace(array('}', '{'), '', $this->guid()));
	}
}