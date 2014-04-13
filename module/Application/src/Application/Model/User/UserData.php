<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./modules/Application/src/Application/Model/User/UserData.php
 */

namespace Application\Model\User;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Sql\Sql;
use Application\Model\AbstractModel;

 /**
 * Application - User Data Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./modules/Application/src/Application/Model/User/UserData.php
 */
class UserData extends AbstractModel
{
	/**
	 * The form validation filering
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $inputFilter;
			
	/**
	 * Contains all the keys for the global settings
	 * @var array
	 */
	public $defaults = array(
		'timezone' => 'America/Los_Angeles', 
		'locale' => 'en_US',  
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
	 * The User UserData Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Sets the input filter to use
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns the InputFilter
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Creates the array for modifying the DB
	 * @param array $data
	 * @return multitype:\PM\Model\Zend_Db_Expr unknown
	 */
	private function getSQL($data){
		return array(
		'option_value' => $data['option_value'],
		'option_name' => $data['option_name'],
		'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	/**
	 * Verifies that a submitted setting is valid and exists. If it's valid but doesn't exist it is created.
	 * @param string $setting
	 */
	private function _checkEntry($data, $identity)
	{
		
		//exit;
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
		return $this->insert('user_data', $sql);
	}
	
	/**
	 * Returns the value straight from the database
	 * @param string $setting
	 */
	public function getUserData($data, $identity)
	{
		$sql = $this->db->select()->from('user_data')->columns(array('id'))
					->where(array('option_name' => $data, 'user_id' => $identity));
		return $this->getRow($sql);
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
		
		$sql = $this->getSQL(array('option_name' => $key, 'option_value' => $value));
		if($this->update('user_data', $sql, array('option_name' => $key, 'user_id' => $identity)))
		{
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
		
		return TRUE;
	}

	/**
	 * Returns the settings array and sets the cache accordingly
	 */
	public function getUsersData($identity)
	{
		$sql = $this->db->select()->from(array('ud' => 'user_data'))->columns(array('option_name', 'option_value'))->where(array('user_id' => $identity));
		$data = $this->_translateEntries($this->getRows($sql));
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