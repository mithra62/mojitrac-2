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
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
	
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $sql)
	{
		$this->adapter = $adapter;
		$this->db = $sql;
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
	
	public function getRows($sql)
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
	
	public function update($table, array $what = null, array $where = null)
	{
		$sql = $this->db->update($table)->set($what)->where($where);
		$updateString = $this->db->getSqlStringForSqlObject($sql);
		return ($this->adapter->query($updateString, 'execute'));
	}	
	

	public function getAdapter()
	{
		if (!$this->adapter) {
			$sm = $this->getServiceLocator();
			$this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
		}
		return $this->adapter;
	}	
}