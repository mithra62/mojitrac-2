<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
 * @filesource 	./module/Application/src/Application/Model/Settings.php
*/

namespace Application\Model;

use Zend\Db\Sql\Sql;

/**
* Setting Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
 * @filesource 	./module/Application/src/Application/Model/Settings.php
*/
class Settings extends AbstractModel
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
	 * Contains all the keys for the client settings
	 * @var array
	 */
	public $client = array('');
	
	/**
	 * Contains all the keys for the global settings
	 * @var array
	 */
	public $global = array(
						   'master_company', 
						   'enable_ip', 
						   'timezone', 
						   'allowed_file_formats', 
						   'date_format', 
						   'date_format_custom', 
						   'time_format', 
						   'time_format_custom'
			);
			
	/**
	 * Contains all the keys for the global settings
	 * @var array
	 */
	public $pm = array(
					  'master_company', 
					  'enable_ip', 
					  'timezone', 
					  'allowed_file_formats', 
					  'date_format',
					  'date_format_custom',  
					  'time_format',  
					  'time_format_custom'
			);			
	
	/**
	 * The Constructor
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Verifies that a submitted setting is valid and exists. If it's valid but doesn't exist it is created.
	 * @param string $setting
	 */
	private function _checkSetting($setting)
	{
		if(in_array($setting, $this->global) || in_array($setting, $this->pm) || in_array($setting, $this->client))
		{
			if(!$this->getSetting($setting))
			{
				$this->addSetting($setting);
			}
			
			return TRUE;
		}		
	}
	
	/**
	 * Adds a setting to the databse
	 * @param string $setting
	 */
	public function addSetting($setting)
	{
		$sql = $this->db->getSQL(array('option_name' => $setting));
		return $this->db->addSetting($sql);
	}
	
	/**
	 * Returns the value straigt from the database
	 * @param string $setting
	 */
	public function getSetting($setting)
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('id'))->where('option_name = ?', $setting);
		return $this->db->getSetting($sql);
	}
	
	/**
	 * Updates the value of a setting
	 * @param string $key
	 * @param string $value
	 */
	public function updateSetting($key, $value)
	{
		if(!$this->_checkSetting($key))
		{
			return FALSE;
		}
		
		$sql = $this->db->getSQL(array('option_name' => $key, 'option_value' => $value));
		return $this->db->updateSetting($sql, $key);
		
	}
	
	/**
	 * Updates all the settings for the provided array
	 * @param array $settings
	 */
	public function updateSettings($settings)
	{
		$ip = new PM_Model_Ips;
		$identity = Zend_Auth::getInstance()->getIdentity();
		foreach($settings AS $key => $value)
		{
			$this->updateSetting($key, $value);
			if($key == 'enable_ip' && $value == '1')
			{				
				$ip->addIp(array('ip' => $_SERVER['REMOTE_ADDR'], 'description' => 'Ip Blocking Enabled'), $identity);
			}
		}
		$this->cache->remove('pm_settings');
		return TRUE;
	}

	/**
	 * Returns the settings array and sets the cache accordingly
	 */
	public function getSettings()
	{
		$sql = $this->db->select()->from(array('s' => 'settings'))->columns( array('option_name', 'option_value'));
		$settings = $this->_translateSettings($this->getRows($sql));
		return $settings;
	}
	
	private function _translateSettings($settings)
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