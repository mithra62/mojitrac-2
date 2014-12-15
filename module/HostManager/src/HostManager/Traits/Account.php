<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Traits/Account.php
 */

namespace HostManager\Traits;

/**
 * HostManager - Account Trait
 *
 * Contains methods for dealing with accounts
 *
 * @package 	MojiTrac\Traits
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/src/HostManager/Traits/Account.php
 */
trait Account
{
	/**
	 * The system account_id we're using
	 * @var int
	 */
	public $account_id = false;
	
	/**
	 * The ZF2 config array
	 * @param array $config
	 */
	public function setConfig(array $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Returns the Account ID
	 * @param array $where
	 * @return int
	 */
	public function getAccountId(array $where = array())
	{
		if( !$this->account_id )
		{
			$parts = parse_url($_SERVER['HTTP_HOST']);
			$sub = str_replace($this->config['sub_primary_url'], '', $parts['path']);
			$sql = $this->db->select()->from(array('a'=> 'accounts'))->columns(array('id'))->where(array('slug' => $sub));
			if( $where )
			{
				$sql = $sql->where($where);
			}
				
			$account = $this->getRow($sql);
			if( !empty($account['id']) )
			{
				$this->account_id = $account['id'];
			}
		}
	
		return $this->account_id;
	}
}