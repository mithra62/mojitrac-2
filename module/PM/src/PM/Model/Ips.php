<?php
/**
 * The IP module model
 * @author Eric
 *
 */
class PM_Model_Ips extends Model_Abstract
{
	
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db = new PM_Model_DbTable_Ips;
	}
	
	/**
	 * Returns the Project Form
	 * @return object
	 */
	public function getIpForm($options = array(), $hidden = array())
	{
        return new PM_Form_Ip($options, $hidden);		
	}	
	
	/**
	 * Returns all the Ip Addresses
	 */
	public function getAllIps()
	{
		$sql = $this->db->select()->from(array($this->db->getTableName()));
		return $this->db->getIps($sql);
	}
	
	/**
	 * Returns the IP for the pk
	 * @param int $id
	 */
	public function getIpById($id)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('ip'=>$this->db->getTableName()));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = ip.creator', array('first_name', 'last_name'));
		$sql = $sql->where('ip.id = ?', $id);
		return $this->db->getIp($sql);
	}
	
	/**
	 * Checks if the provided Ip Address is allowed in the system
	 * @param int $ip
	 */
	public function isAllowed($ip)
	{
		$key = 'ip_'.ip2long($ip);
		if(!$data = $this->cache->load($key)) 
		{		
			$sql = $this->db->select()->from(array('ip' => $this->db->getTableName()), array('id'))->where('ip = ?', new Zend_Db_Expr("INET_ATON('$ip')"));
			$data = $this->db->getIp($sql);
			$this->cache->save($data, $key, array($this->cache_keys['ips']));
		}
		return $data;
	}
	
	/**
	 * Adds an Ip Address to the white list
	 * @param array $data
	 * @param int $creator
	 */
	public function addIp(array $data, $creator)
	{
		if($this->isAllowed($data['ip']))
		{
			return TRUE;
		}
		$sql = $this->db->getSQL($data);
		$sql['creator'] = $creator;
		if($this->db->addIp($sql))
		{
		    $this->cache->clean(
		          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
		          array($this->cache_keys['ips'])
		    );
		    return TRUE;			
		}	
	}
	
	/**
	 * Removes an Ip Address from the white list
	 * @param string $key
	 * @param stirng $col
	 */
	public function removeIp($key, $col = 'id')
	{
		if($this->db->deleteIp($key, $col))
		{
		    $this->cache->clean(
		          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
		          array($this->cache_keys['ips'])
		    );
		    return TRUE;			
		}
	}
	
	/**
	 * Updates an Ip Address on the white list
	 * @param array $data
	 * @param int $id
	 */
	public function updateIp(array $data, $id)
	{
		$sql = $this->db->getSQL($data);
		if($this->db->update($sql, "id = '$id'"))
		{
		    $this->cache->clean(
		          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
		          array($this->cache_keys['ips'])
		    );
		    return TRUE;	
		}
	}
}