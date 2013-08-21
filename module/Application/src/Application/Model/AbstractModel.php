<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/controllers/LoginController.php
 */

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

 /**
 * Model Abstract
 *
 * Sets things up for abstracted functionality
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/models/Abstract.php
 */
abstract class AbstractModel
{
	/**
	 * The database object
	 * @var object
	 */
	protected $db;
	
	/**
	 * The Cache Object
	 * @var object
	 */
	public $cache;

	/**
	 * The stored cache name
	 * @var string
	 */
	public $cache_key = null;
		
	public $events = null;
	
	protected $adapter = null;
	
	public function __construct($adapter = null)
	{
		$this->adapter = $adapter;
		$this->db = new SQL($this->getAdapter());
	}
	
	public function getRow($sql)
	{
		$selectString = $this->db->getSqlStringForSqlObject($sql);
		$result = $this->adapter->query($selectString, 'execute')->toArray();
		if(!empty($result['0']))
		{
			return $result['0']; 
		}
		else
		{
			return array();
		}
	}
	
	public function getRows()
	{
		
	}
	
	public function update($table, array $what = null, array $where = null)
	{
		$sql = $this->db->update($table)->set($what)->where($where);
		$updateString = $this->db->getSqlStringForSqlObject($sql);
		return ($this->adapter->query($updateString, 'execute'));
	}
	
	public function getAdapter()
	{		
		return $this->adapter;
	}
	
	/**
	 * Wraps up the Event functionality nice and tidy
	 * @param Zend_EventManager_EventCollection $events
	 * @return Ambigous <Zend_EventManager_EventManager, Zend_EventManager_EventCollection>
	 */
	public function events(Zend_EventManager_EventCollection $events = null)
	{
		if (null !== $events) {
			$this->events = $events;
		} elseif (null === $this->events) {
			$this->events = Zend_Registry::get('event_manager');
		}
		return $this->events;
	}

	public function event($name, $obj, $argv)
	{
		return $this->events()->trigger($name, $obj, $argv);
	}
}