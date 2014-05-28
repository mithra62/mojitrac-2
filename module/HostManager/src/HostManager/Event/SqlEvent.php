<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Event/SqlEvent.php
 */

namespace HostManager\Event;

use Base\Event\BaseEvent, Exception;

 /**
 * HostManager - SQL Events
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/src/HostManager/Event/SqlEvent.php
 */
class SqlEvent extends BaseEvent
{
	/**
	 * What account we're limiting things to
	 * @var int
	 */
	public $account_id = null;
	
    /**
     * User Identity
     * @var int
     */
    public $identity = false;
    
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'db.select.pre' => 'selectPre',
        'db.insert.pre' => 'insertPre',
        'db.remove.pre' => 'removePre',
        'db.update.pre' => 'updatePre',
    );
    
    /**
     * The Hosted SQL Event
     * @param int $identity
     */
    public function __construct($identity = null, \HostManager\Model\Accounts $account = null, $base_url = null)
    {
        $this->identity = $identity;
        $this->account = $account;
        $this->base_url = $base_url;
        $this->account_id = $this->getAccountId();
    }

    /**
     * Registers the Event with ZF and our Application Model
     * @param \Zend\EventManager\SharedEventManager $ev
     */
    public function register( \Zend\EventManager\SharedEventManager $ev)
    {
    	foreach($this->hooks AS $key => $value)
    	{
    		$ev->attach('Base\Model\BaseModel', $key, array($this, $value));
    	}
    }
    
    /**
     * Returns which account_id we're working with
     * 
     * Parses the URL and uses the sudomain slug to determine the account.
     * @return number
     */
    public function getAccountId($forced = FALSE)
    {
    	if( !$this->account_id || $forced)
    	{
	    	$parts = parse_url($_SERVER['HTTP_HOST']);
			$sub = str_replace($this->base_url, '', $parts['path']);
	    	$this->account_id = $this->account->getAccountId(array('slug' => $sub));
	    	if( !$this->account_id )
	    	{
	    		//do some error handling
	    		throw(new \Exception('NO!!!'));
	    	}
    	}
    	
    	return $this->account_id;
    }
    
    /**
     * Returns the name of the table we're working
     * 
     * Parses the output from getRawState() to determine which table we're working with.
     * 
     * @param mixed $table
     * @return string
     */
    public function getTableName($table)
    {
    	if( is_array($table) )
    	{
    		$string = '';
    		foreach($table AS $key => $value)
    		{
    			$parts = explode('_', $value);
    			$parts = array_map('ucfirst', $parts);
    			$string = implode('', $parts);
    		}
    		
    		$table = $string;
    	}
    	else 
    	{
    		$parts = explode('_', $table);
    		$parts = array_map('ucfirst', $parts);
    		$string = implode('', $parts);
    		$table = $string;
    	}
    	
    	return $table;
    }
    
    /**
     * Modifies all the SELECT calls to inject account_id to all WHERE clauses (where appropriate)
     * @param \Zend\EventManager\Event $event
     */
    public function selectPre(\Zend\EventManager\Event $event)
    {
    	$sql = $event->getParam('sql');
    	$raw_data = $sql->getRawState();
    	$table_name = $this->getTableName($raw_data['table']);
    	try {
    		$class_name = "HostManager\Model\Sql\\".$table_name;
    		if(class_exists($class_name))
    		{
    			$class = new $class_name($sql);
    			$sql = $class->Select($sql, $this->account_id);
    		}
    	}
    	catch (Exception $e)
    	{
    		return $sql;
    	}
    	
		return $sql;
    }

    /**
     * Modifies all the INSERT calls to inject account_id into statements (where appropriate)
     * @param \Zend\EventManager\Event $event
     */
    public function insertPre(\Zend\EventManager\Event $event)
    {
    	$sql = $event->getParam('sql');
    	$raw_data = $sql->getRawState();
    	$table_name = $this->getTableName($raw_data['table']);
    	try {
    		$class_name = "HostManager\Model\Sql\\".$table_name;
    		if(class_exists($class_name))
    		{
    			$class = new $class_name($sql);
    			$sql = $class->Insert($sql, $this->account_id);
    		}
    	}
    	catch (Exception $e)
    	{
    		return $sql;
    	}
    	 
    	return $sql;
    }  
    
    /**
     * Modifies all the DELETE calls to inject account_id into statements (where appropriate)
     * @param \Zend\EventManager\Event $event
     */
    public function removePre(\Zend\EventManager\Event $event)
    {
    	$sql = $event->getParam('sql');
    	$raw_data = $sql->getRawState();
    	$table_name = $this->getTableName($raw_data['table']);
    	try {
    		$class_name = "HostManager\Model\Sql\\".$table_name;
    		if(class_exists($class_name))
    		{
    			$class = new $class_name($sql);
    			$sql = $class->Delete($sql, $this->account_id);
    		}
    	}
    	catch (Exception $e)
    	{
    		return $sql;
    	}
    
    	return $sql;
    }  

    /**
     * Modifies all the UPDATE calls to inject account_id into statements (where appropriate)
     * @param \Zend\EventManager\Event $event
     */
    public function updatePre(\Zend\EventManager\Event $event)
    {
    	$sql = $event->getParam('sql');
    	$raw_data = $sql->getRawState();
    	$table_name = $this->getTableName($raw_data['table']);
    	try {
    		$class_name = "HostManager\Model\Sql\\".$table_name;
    		if(class_exists($class_name))
    		{
    			$class = new $class_name($sql);
    			$sql = $class->Update($sql, $this->account_id);
    		}
    	}
    	catch (Exception $e)
    	{
    		return $sql;
    	}
    
    	return $sql;
    }        
}