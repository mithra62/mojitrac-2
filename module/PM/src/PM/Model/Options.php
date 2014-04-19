<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Options.php
 */

namespace PM\Model;

use Application\Model\AbstractModel;

 /**
 * PM - Options Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Options.php
 */
class Options extends AbstractModel
{
	public $cache_key = 'options';
	
	/**
	 * The system areas where options are stored
	 * @var array
	 */
	public $areas = array(
						 'project_type' => 'project_type',
						 'task_type' => 'task_type'
	);
	
	/**
	 * The System Options
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Returns all the options for the project types
	 */
	public function getAllProjectTypes()
	{		
			$sql = $this->db->select()->from('options')->columns(array('id', 'name'))
									  ->where(array('area' => $this->areas['project_type']))
									  ->order('name ASC');
			$types = $this->getRows($sql);
		return $types;
	}
	
	/**
	 * Returns all the options for the project types
	 */
	public function getAllTaskTypes()
	{	
		$sql = $this->db->select()->from('options')->columns(array('id', 'name'))
								  ->where(array('area' => $this->areas['task_type']))
								  ->order('name ASC');
		$types = $this->getRows($sql);
		return $types;
	}
	
	public function getOptions($key = FALSE, $value = FALSE, $area)
	{
		$sql = $this->db->select();
		return $this->db->getOptions($sql);		
	}	
	
	/**
	 * Returns all the Options
	 */
	public function getAllOptions()
	{
		$sql = $this->db->select()->from('options');
		return $this->getRows($sql);
	}
	
	public function getOptionById($id)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('o'=>$this->db->getTableName()));
		$sql = $sql->where('o.id = ?', $id);
		return $this->db->getOption($sql);
	}
	
	/**
	 * Adds an Option to the db
	 * @param array $data
	 * @param int $creator
	 */
	public function addOption(array $data, $creator)
	{
		$sql = $this->db->getSQL($data);
		$sql['creator'] = $creator;
		if($id = $this->db->addOption($sql))
		{
		    $this->cache->clean(
		          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
		          array($this->cache_key)
		    );
		    return $id;			
		}	
	}
	
	/**
	 * Removes an option and updates all the entries for that option
	 * @param string $key
	 * @param stirng $col
	 */
	public function removeOption($key, $col = 'id')
	{
		if($this->db->deleteOption($key, $col))
		{
		    $this->cache->clean(
		          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
		          array($this->cache_key)
		    );
		    return TRUE;			
		}
	}
	
	/**
	 * Updates an Ip Address on the white list
	 * @param array $data
	 * @param int $id
	 */
	public function updateOption(array $data, $id)
	{
		$sql = $this->db->getSQL($data);
		if($this->db->update($sql, "id = '$id'"))
		{
		    $this->cache->clean(
		          Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
		          array($this->cache_key)
		    );
		    return TRUE;	
		}
	}
}