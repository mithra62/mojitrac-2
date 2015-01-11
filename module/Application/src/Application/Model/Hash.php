<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Hash.php
*/

namespace Application\Model;

use Zend\Crypt\BlockCipher;

/**
 * Hash Model
 *
 * @package 	Security\Hash
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Application/src/Application/Model/Hash.php
*/
class Hash
{
	/**
	 * The default encryption key to use
	 * @var string
	 */
	protected $key = 'c6PKuC t(b]a))NIuM)eX>^82+4kvk!@!v(eXM{yGW9yLFC A0D0X;]8pQ0@Iv~|';
	
	private $block_cipher = null;
	
	/**
	 * Creates a hash to use for salting
	 * @return string
	 */
	public function gen_salt()
	{
		return md5(microtime());
	}
	
	/**
	 * Encrypts up the password for storage
	 * @param string $password
	 * @param string $salt
	 * @return string
	 */
	public function password($password, $salt)
	{
		$salt = hash_hmac('sha512', $salt, $this->key);
		return hash_hmac('sha512', $password . $salt, $this->key);
	}
	
	/**
	 * Creates a standard GUID string
	 * @return string
	 */
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
	
	/**
	 * Returns a GUID without wrapping brackets
	 * @return string
	 */
	public function guidish()
	{
		return strtolower(str_replace(array('}', '{'), '', $this->guid()));
	}
	
	public function encrypt($string)
	{
		$this->block_cipher->setKey($this->key);
		return $this->block_cipher->encrypt($string);
	}
	
	public function decrypt($string)
	{
		
	}
	
	private function getBlockCypher()
	{
		if(!$this->block_cipher)
		{
			$this->block_cipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
		}
		
		return $this->block_cipher;
	}
}