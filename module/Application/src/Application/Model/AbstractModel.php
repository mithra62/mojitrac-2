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

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;

 /**
 * Model Abstract
 *
 * Sets things up for abstracted functionality
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/models/Abstract.php
 */
abstract class AbstractModel implements EventManagerInterfaceConstants
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
	
	/**
	 * The Event Manager Object
	 * @var \Zend\EventManager\EventManager
	 */
	public $events = null;
	
	/**
	 * The Database Adapter
	 * @var \Zend\Db\Adapter\Adapter
	 */
	protected $adapter = null;
	
	/**
	 * Moji Abstract Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $sql
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $sql)
	{
		$this->adapter = $adapter;
		$this->db = $sql;
	}

	/**
	 * Returns single row from $sql
	 * @param \Zend\Db\Sql\Select $sql
	 * @return array:
	 */
	public function getRow(\Zend\Db\Sql\Select $sql)
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
	
	/**
	 * Returns multiple rows from $sql
	 * @param \Zend\Db\Sql\Select $sql
	 * @return array:
	 */
	public function getRows(\Zend\Db\Sql\Select $sql)
	{
		$selectString = $this->db->getSqlStringForSqlObject($sql);
		
		//echo $selectString.'<br /><br />';
		$result = $this->adapter->query($selectString, 'execute')->toArray();
		if(!empty($result))
		{
			return $result;
		}
		else
		{
			return array();
		}		
	}
	
	/**
	 * Updates $table with $what based on $where
	 * @param string $table
	 * @param array $what
	 * @param array $where
	 */
	public function update($table, array $what = null, array $where = null)
	{
		$sql = $this->db->update($table)->set($what)->where($where);
		$updateString = $this->db->getSqlStringForSqlObject($sql);
		return ($this->adapter->query($updateString, 'execute'));
	}

	/**
	 * Creates a new entry in $table
	 * @param string $table
	 * @param array $data
	 * @return Ambigous <\Zend\Db\Adapter\Driver\mixed, NULL>
	 */
	public function insert($table, array $data)
	{
		$sql = $this->db->insert($table)->values($data);
		$insertString = $this->db->getSqlStringForSqlObject($sql);		
		$result = ($this->adapter->query($insertString, 'execute'));
		return $result->getGeneratedValue(); 
	}
	
	public function remove($table, array $where)
	{
		
	}
	
	/**
	 * Returns the databse adapter or lazy loads it if it doesn't exist
	 */
	public function getAdapter()
	{
		if (!$this->adapter) {
			$sm = $this->getServiceLocator();
			$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
		}
		return $this->adapter;
	}	
	
	/**
	 * Creates an instance of the Event Manager
	 * @param EventManagerInterface $events
	 * @return \Application\Model\AbstractModel
	 */
	public function setEventManager(EventManagerInterface $events)
	{
		$events->setIdentifiers(array(
				__CLASS__,
				get_called_class(),
		));
		$this->events = $events;
		return $this;
	}
	
	public function getEventManager()
	{
		if (null === $this->events) {
			$this->setEventManager(new EventManager());
		}
		return $this->events;
	}

	public function event($name, $obj, $argv)
	{
		return $this->getEventManager()->trigger($name, $obj, $argv);
	}	
}