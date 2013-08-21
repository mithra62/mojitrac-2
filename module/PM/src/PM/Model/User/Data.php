<?php
class PM_Model_User_Data extends Model_Abstract
{
	/**
	 * The link to the database object
	 * @var object
	 */
	public $db;
	
	/**
	 * The cache object
	 * @var object
	 */
	public $cache;
			
	/**
	 * Contains all the keys for the global settings
	 * @var array
	 */
	public $defaults = array(
					  'timezone', 
					  'date_format',
					  'date_format_custom',  
					  'time_format',  
					  'time_format_custom',
					  'noti_assigned_task',
					  'noti_status_task',
					  'noti_priority_task',
					  'noti_daily_task_reminder',
					  'noti_add_proj_team',
					  'noti_remove_proj_team',
					  'noti_file_uploaded',
					  'noti_file_revision_uploaded',
					  'timer_data',
					  'enable_rel_time',
					  'enable_contextual_help'
			);			
	
	/**
	 * The Constructor
	 */
	public function __construct()
	{		
		parent::__construct();		
		$this->db = new PM_Model_DbTable_User_Data;
	}	
	
	/**
	 * Verifies that a submitted setting is valid and exists. If it's valid but doesn't exist it is created.
	 * @param string $setting
	 */
	private function _checkEntry($data, $identity)
	{
		if(in_array($data, $this->defaults))
		{
			if(!$this->getUserData($data, $identity))
			{
				$this->addUserData($data, $identity);
			}
			
			return TRUE;
		}		
	}
	
	/**
	 * Adds a setting to the databse
	 * @param string $setting
	 */
	public function addUserData($data, $identity)
	{
		$sql = $this->db->getSQL(array('option_name' => $data));
		$sql['user_id'] = $identity;
		return $this->db->addUserData($sql);
	}
	
	/**
	 * Returns the value straight from the database
	 * @param string $setting
	 */
	public function getUserData($data, $identity)
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('id'))->where('option_name = ?', $data)->where('user_id = ?', $identity);
		return $this->db->getUserData($sql);
	}
	
	/**
	 * Updates the value of a setting
	 * @param string $key
	 * @param string $value
	 */
	public function updateUserDataEntry($key, $value, $identity)
	{
		if(!$this->_checkEntry($key, $identity))
		{
			return FALSE;
		}
		
		$sql = $this->db->getSQL(array('option_name' => $key, 'option_value' => $value));
		if($this->db->update($sql, "option_name = '".$key."' AND user_id = '$identity'"))
		{
			$this->cache->remove($identity.'_user_data');
			return TRUE;
		}
		
	}
		
	/**
	 * Updates all the settings for the provided array
	 * @param array $settings
	 */
	public function updateUserData($data, $identity)
	{
		foreach($data AS $key => $value)
		{
			$this->updateUserDataEntry($key, $value, $identity);
		}
		$this->cache->remove($identity.'_user_data');
		return TRUE;
	}

	/**
	 * Returns the settings array and sets the cache accordingly
	 */
	public function getUsersData($identity)
	{
		if(!$data = $this->cache->load($identity.'_user_data')) 
		{
			$sql = $this->db->select()->from(array('ud' => $this->db->getTableName()), array('option_name', 'option_value'))->where('user_id = ?', $identity);
			$data = $this->_translateEntries($this->db->getUsersData($sql));
			$this->cache->save($data, $identity.'_user_data');
		}
		
		return $data;
	}
	
	private function _translateEntries($settings)
	{
		$arr = array();
		foreach($settings AS $setting)
		{
			if(in_array(strtolower($setting['option_value']), array('1', 'true', 'yes')))
			{
				$arr[$setting['option_name']] = TRUE;
			}
			elseif(in_array(strtolower($setting['option_value']), array('0', 'false', 'no')))
			{
				$arr[$setting['option_name']] = FALSE;
			}
			else
			{
				$arr[$setting['option_name']] = $setting['option_value'];
			}
		}
		
		return $arr;
	}
	
}