<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Ips.php
*/

/**
* PM - Ips Database Model
*
* Returns the Ips Database Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/modeles/DbTable/Ips.php
*/
class PM_Model_DbTable_Ips extends Model_DbTable_Abstract
{
   /**
     * Table name
     * @var string
     */
	protected $_name = "ips";
	
	/**
	 * Returns the Ips table name
	 * @return string
	 */	
	public function getTableName()
	{
		return $this->_name;
	}	
    
	/**
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
		'ip' => ip2long($data['ip']),
		'ip_raw' => $data['ip'],
		'description' => $data['description'],
		'last_modified' => new Zend_Db_Expr('NOW()')
		);
	}
	
	/**
	 * Returns an array of a Ips
	 * @param $where
	 * @return mixed
	 */
	public function getIp($where)
	{
		$data = $this->fetchRow($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$task_data = $data->toArray();
		return $task_data;
	}
	
	/**
	 * Returns more than 1 Ip
	 * @param $where
	 * @return mixed
	 */
	public function getIps($where)
	{
		$data = $this->fetchAll($where);
		if(!$data){
			///throw new Exception("Count not find row $where");
			return FALSE;
		} 
		
		$tasks = $data->toArray();		
		return $tasks;
	}
	
	/**
	 * Update a Ip
	 * @param $data
	 * @param $id
	 * @return void
	 */
	public function updateIp($data, $id)
	{
		return $this->update($data, 'id = '. $id);
	}	
	
	/**
	 * Adds a Ip
	 * @param $data
	 * @return int
	 */
	public function addIp($data)
	{
		$data['created_date'] = new Zend_Db_Expr('NOW()');
		return $this->insert($data);
	}
	
	/**
	 * Deletes a Ip
	 * @param $id
	 * @return bool
	 */
	function deleteIp($id, $col = 'id')
	{
		$where = parent::getAdapter()->quoteInto($col . ' = ?', $id);
		return $this->delete($where);
	}	
	
}