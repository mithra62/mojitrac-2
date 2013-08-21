<?php
class PM_Model_Options extends Model_Abstract
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
	
	public function __construct()
	{	
		parent::__construct();
		$this->db = new PM_Model_DbTable_Options;
	}
	
	/**
	 * Returns all the options for the project types
	 */
	public function getAllProjectTypes()
	{
		$key = 'options_'.$this->areas['project_type'];
		if(!$types = $this->cache->load($key)) 
		{			
			$sql = $this->db->select()->from(array($this->db->getTableName()), array('id', 'name'))
									  ->where('area = ?', $this->areas['project_type'])
									  ->order('name ASC');
			$types = $this->db->getOptions($sql);
			$this->cache->save($types, $key, array($this->cache_key));
		}
		return $types;
	}
	
	/**
	 * Returns all the options for the project types
	 */
	public function getAllTaskTypes()
	{
		$key = 'options_'.$this->areas['task_type'];
		if(!$types = $this->cache->load($key)) 
		{			
			$sql = $this->db->select()->from(array($this->db->getTableName()), array('id', 'name'))
									  ->where('area = ?', $this->areas['task_type'])
									  ->order('name ASC');
			$types = $this->db->getOptions($sql);
			$this->cache->save($types, $key, array($this->cache_key));
		}
		return $types;
	}
	
	public function getOptions($key = FALSE, $value = FALSE, $area)
	{
		$sql = $this->db->select();
		return $this->db->getOptions($sql);		
	}
	
	/**
	 * Returns the Project Form
	 * @return object
	 */
	public function getOptionsForm($options = array())
	{
        return new PM_Form_Option($options, $hidden);		
	}	
	
	/**
	 * Returns all the Options
	 */
	public function getAllOptions()
	{
		$sql = $this->db->select()->from(array($this->db->getTableName()));
		return $this->db->getOptions($sql);
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