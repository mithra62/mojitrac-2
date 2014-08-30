<?php
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./modules/Application/src/Application/Model/User/UserData.php
 */

namespace Application\Model\User;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Base\Model\KeyValue;

/**
 * Application - User Data Model
 *
 * @package 	Users\UserData
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./modules/Application/src/Application/Model/User/UserData.php
 */
class UserData extends KeyValue
{
	/**
	 * The form validation filering
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $inputFilter;

	/**
	 * The databaes table the Moji Settings are stored in
	 * @var string
	 */
	public $table = 'user_data';	
			
	/**
	 * Contains all the keys for the global settings
	 * @var array
	 */
	public $defaults = array(
		'timezone' => 'America/Los_Angeles', 
		'locale' => 'en_US',  
		'date_format' => 'F j, Y',
		'date_format_custom' => '',  
		'time_format' => 'g:i A',  
		'time_format_custom' => '',
		'noti_assigned_task' => '1',
		'noti_status_task' => '1',
		'noti_priority_task' => '1',
		'noti_daily_task_reminder' => '1',
		'noti_add_proj_team' => '1',
		'noti_remove_proj_team' => '1',
		'noti_file_uploaded' => '1',
		'noti_file_revision_uploaded' => '1',
		'timer_data' => '0',
		'enable_rel_time' => '1',
		'enable_contextual_help' => '1'
	);			
	
	/**
	 * The User UserData Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
		$this->setTable($this->table);
		$this->setDefaults($this->defaults);
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
	public function getSQL(array $data, $create = TRUE){
		$sql = array(
			'option_value' => $data['option_value'],
			'option_name' => $data['option_name'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
		
		if($create)
		{
			$sql['user_id'] = $this->identity;
		}
		
		return $sql;
	}
	
	/**
	 * Returns the value straight from the database
	 * @param string $setting
	 */
	public function getUserData($data, $identity)
	{;
		return $this->getItem($data, array('user_id' => $identity));
	}
	
	/**
	 * Updates the value of a setting
	 * @param string $key
	 * @param string $value
	 */
	public function updateUserDataEntry($key, $value, $identity)
	{
		$where = array('user_id' => $identity);
		$this->identity = $identity;
		if(!$this->checkItem($key, $where))
		{
			return FALSE;
		}
	
		return parent::updateItem($key, $value, $where);
	}	
		
	/**
	 * Updates all the settings for the provided array
	 * @param array $settings
	 */
	public function updateUserData($data, $identity)
	{
		$this->identity = $identity;
		return parent::updateItems($data, array('user_id' => $identity));
	}

	/**
	 * Returns the settings array and sets the cache accordingly
	 */
	public function getUsersData($identity)
	{
		return parent::getItems(array('user_id' => $identity));
	}
}