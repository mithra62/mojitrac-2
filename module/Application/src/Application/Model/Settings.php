<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/forms/Settings.php
*/

/**
* Setting Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/models/Settings.php
*/
class Model_Settings
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
	public function __construct()
	{
		
		$c = new Model_Cache;
		// getting a Zend_Cache_Core object
		$this->cache = Zend_Cache::factory(
	                    $c->getFrontendType(),
	                    $c->getBackendType(),
	                    $c->getFrontendOptions(),
	                    $c->getBackendOptions()
		);		
			
		$this->db = new Model_DbTable_Settings;
	}
	
	/**
	 * Returns the form for managing the global settings
	 * @param array $options
	 * @param array $hidden
	 */
	public function getSettingForm($options = array(), $hidden = array())
	{
        return new PM_Form_Settings($options, $hidden);		
	}
	
	/**
	 * Returns the form for managing the global settings
	 * @param array $options
	 * @param array $hidden
	 */
	public function getResetForm($options = array(), $phrase = FALSE)
	{
        return new PM_Form_Reset($options, $phrase);		
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
		if(!$settings = $this->cache->load('pm_settings')) {
			$sql = $this->db->select()->from(array('s' => $this->db->getTableName()), array('option_name', 'option_value'));
			$settings = $this->_translateSettings($this->db->getSettings($sql));
			$this->cache->save($settings, 'pm_settings');
		}
				
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