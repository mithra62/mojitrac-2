<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Model/
 */

namespace Base\Model;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;

 /**
 * Base Abstract
 *
 * General Moji Model Methods
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Base/src/Base/Model/BaseModel.php
 */
abstract class BaseModel implements EventManagerInterfaceConstants
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
	public function __construct(\Zend\Db\Adapter\Adapter $adapter = null, \Zend\Db\Sql\Sql $sql = null)
	{
	    if($adapter && $sql)
	    {
            $this->adapter = $adapter;
            $this->db = $sql;
	    }
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
	
	/**
	 * Removes rows from $table based on $where
	 * @param string $table
	 * @param array $where
	 * @return number
	 */
	public function remove($table, array $where)
	{
	    $sql = $this->db->delete($table)->where($where);
		$removeString = $this->db->getSqlStringForSqlObject($sql);	
		$result = ($this->adapter->query($removeString, 'execute'));
		return $result->getAffectedRows(); 
	}
	
	public function query($sql)
	{
	    $result = ($this->adapter->query($sql, 'execute'));
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
		        'Moji',
		));
		$this->events = $events;
		return $this;
	}
	
	/**
	 * Returns an instance of the Event Manager (or creates one if it doesn't exist yet)
	 * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
	 */
	public function getEventManager()
	{
		if (null === $this->events) {
			$this->setEventManager(new EventManager());
		}
		return $this->events;
	}

	/**
	 * Wrapper for trigger Event Manager hooks
	 * @param mixed $names
	 * @param object $obj
	 * @param array $argv
	 * @param array $xhooks
	 * @return object
	 */
	public function trigger($names, $obj, array $argv, array $xhooks = array())
	{
	    if(!is_array($names))
	    {
	        $names = array($names);
	    }
	    
	    //setup the "special" context sensitive hooks
	    foreach($names AS $name)
	    {
	        foreach($xhooks AS $key => $value)
	            foreach($value As $context => $pk)
	               $names[] = $name.'['.$context.'.'.$pk.']';
	    }
	    
	    $names = (array_reverse($names));
	    
		$argv = $this->getEventManager()->prepareArgs($argv);
		foreach($names AS $event)
		{
            $ext = $this->getEventManager()->trigger($event, $obj, $argv);
            if($ext->stopped()) 
            {
                return $ext; 
            }
		}
		
		return $ext;
	}
	
	/**
	 * Sets up the contextual hooks based on $data
	 * @param array $data
	 * @return array
	 */
	public function setXhooks(array $data = array())
	{
		$return = array();
		if(!empty($data['company']))
			$return[] = array('company' => $data['company']);
	
		if(!empty($data['project']))
			$return[] = array('project' => $data['project']);
	
		if(!empty($data['priority']))
			$return[] = array('priority' => $data['priority']);
	
		if(!empty($data['type']))
			$return[] = array('type' => $data['type']);
	
		if(!empty($data['status']))
			$return[] = array('status' => $data['status']);
	
		return $return;
	}	
	
	/**
	 * Resolves $path to determine which module path should be created
	 * @param string $path
	 * @return string
	 */
	public function getModulePath($path)
	{
		return realpath($path.'/../../../view/emails');
	}
}