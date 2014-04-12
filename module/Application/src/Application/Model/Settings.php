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
	 * Contains all the defaults for the global settings
	 * @var array
	 */
	private $defaults = array(
	  'master_company' => '1', 
	  'enable_ip' => '0', 
	  'timezone' => 'America/Los_Angeles', 
	  'locale' => 'en_US', 
	  'allowed_file_formats' => 'jpg,gif,png,txt,docx,doc,pdf,php,xls,xlsx,csv,psd,ppt,pptx,pot,potx,rar,zip,tar,gz,tgz,bz2,html,htm,avi,mov,fla,swf,asf,flv,sql,mp3', 
	  'date_format' => 'F j, Y',
	  'date_format_custom' => '',  
	  'time_format' => 'g:i A',  
	  'time_format_custom' => ''
	);	

	/**
	 * The system setttings array
	 * @var array
	 */
	private $settings = array();
	
	/**
	 * The Constructor
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	public function getSQL($data){
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
	private function _checkSetting($setting)
	{
		if(array_key_exists($setting, $this->defaults))
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
		$sql = $this->getSQL(array('option_name' => $setting));
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		return $this->insert('settings', $sql);
	}
	
	/**
	 * Returns the value straigt from the database
	 * @param string $setting
	 */
	public function getSetting($setting)
	{
		$sql = $this->db->select()->from('settings')->columns( array('id'))->where(array('option_name' => $setting));
		return $this->getRow($sql);
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
		
		$sql = $this->getSQL(array('option_name' => $key, 'option_value' => $value));
		return $this->update('settings', $sql, array('option_name' => $key));
	}
	
	/**
	 * Updates all the settings for the provided array
	 * @param array $settings
	 */
	public function updateSettings($settings)
	{
		foreach($settings AS $key => $value)
		{
			$this->updateSetting($key, $value);
			if($key == 'enable_ip' && $value == '1')
			{				
				//ip->addIp(array('ip' => $_SERVER['REMOTE_ADDR'], 'description' => 'Ip Blocking Enabled'), $identity);
			}
		}
		
		return TRUE;
	}

	/**
	 * Returns the settings array and sets the cache accordingly
	 */
	public function getSettings()
	{
		if(!$this->settings)
		{
			$sql = $this->db->select()->from(array('s' => 'settings'))->columns( array('option_name', 'option_value'));
			$this->settings = $this->_translateSettings($this->getRows($sql));
		}
		return $this->settings;
	}
	
	private function _translateSettings(array $settings)
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
		
		//now we verify there are settings for everything 
		foreach($this->defaults AS $key => $value)
		{
		    if(!isset($arr[$key]))
		    {
		        $arr[$key] = $value;
		    }
		}
		
		return $arr;
	}
	
}