<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Model/
 */

namespace Base\Model;

 /**
 * Key Value Abstract
 *
 * Abstracts handling of key => value style database tables
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Base/src/Base/Model/KeyValue.php
 */
abstract class KeyValue extends BaseModel
{	
	/**
	 * The database table we're working with
	 * @var string
	 */
	private $table = null;
	
	/**
	 * The data retrieved from the queries
	 * @var array
	 */
	private $items = array();
	
	/**
	 * The default values to use if none is found
	 * @var array
	 */
	private $defaults = array();
	
	/**
	 * Abstracts handling of key => value style database tables
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $sql
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter = null, \Zend\Db\Sql\Sql $sql = null)
	{
		parent::__construct($adapter, $sql);
	}
	
	/**
	 * Wrapper to create the SQL array for updating/creating an entry
	 * @param array $data
	 * @param string $create
	 */
	abstract public function getSQL(array $data, $create = FALSE);
	
	/**
	 * Sets the database table name
	 * @param string $table
	 */
	public function setTable($table)
	{
		$this->table = $table;	
	}
	
	/**
	 * Sets the default values to assume
	 * @param array $defaults
	 */
	public function setDefaults(array $defaults)
	{
		$this->defaults = $defaults;
	}
	
	/**
	 * Verifies that a submitted setting is valid and exists. If it's valid but doesn't exist it is created.
	 * @param string $setting
	 */
	private function checkItem($item, array $where = array())
	{
		if(array_key_exists($item, $this->defaults))
		{
			$check = $this->getItem($item, $where);
			if(!$check)
			{
				$this->addItem($item);
			}
				
			return TRUE;
		}
	}
	
	/**
	 * Adds a setting to the databse
	 * @param string $setting
	 */
	public function addItem($item)
	{
		$sql = $this->getSQL(array('option_name' => $item), TRUE);
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		return $this->insert($this->table, $sql);
	}
	
	/**
	 * Returns the individual item
	 * @param string $item
	 */
	public function getItem($item, array $where = array())
	{
		$sql = $this->db->select()->from($this->table)->where(array('option_name' => $item));
		if($where)
		{
			$sql->where($where);
		}
		
		$result = $this->getRow($sql);
		return $this->getRow($sql);
	}
	
	/**
	 * Updates the value of a setting
	 * @param string $key
	 * @param string $value
	 */
	public function updateItem($key, $value, array $where = array())
	{
		if(!$this->checkItem($key, $where))
		{
			return FALSE;
		}
	
		$where['option_name'] = $key;
		$sql = $this->getSQL(array('option_name' => $key, 'option_value' => $value), FALSE);
		return $this->update($this->table, $sql, $where);
	}
	
	/**
	 * Updates all the settings for the provided array
	 * @param array $items
	 */
	public function updateItems(array $items, array $where = array())
	{
		foreach($items AS $key => $value)
		{
			$this->updateItem($key, $value, $where);
		}
	
		return TRUE;
	}
	
	/**
	 * Returns the settings array and sets the cache accordingly
	 */
	public function getItems(array $where = array())
	{
		if(!$this->items)
		{
			$sql = $this->db->select()->from($this->table)->columns( array('option_name', 'option_value'));
			$this->items = $this->translateItems($this->getRows($sql));
		}
		
		return $this->items;
	}
	
	/**
	 * Takes the key=>value db rows and creates an associative array
	 * @param array $items
	 * @return multitype:boolean array
	 */
	private function translateItems(array $items)
	{
		$arr = array();
		foreach($items AS $item)
		{
			if(in_array(strtolower($item['option_value']), array('1', 'true', 'yes')))
			{
				$arr[$item['option_name']] = '1';
			}
			elseif(in_array(strtolower($item['option_value']), array('0', 'false', 'no')))
			{
				$arr[$item['option_name']] = '0';
			}
			else
			{
				$arr[$item['option_name']] = $item['option_value'];
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