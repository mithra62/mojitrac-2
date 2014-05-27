<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Event/BaseEvent.php
 */

namespace Base\Event;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Base - Base Events
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Base/src/Base/Event/BaseEvent.php
 */
abstract class BaseEvent extends AbstractPluginManager 
{
	/**
	 * (non-PHPdoc)
	 * @see \Zend\ServiceManager\AbstractPluginManager::validatePlugin()
	 */
    public function validatePlugin($plugin)
    {
        
    }
    
    /**
     * Registers the Event with the system
     * 
     * This method should handle attaching the events to 
     * the Base\Model\BaseModel identifier.
     * 
     * @param \Zend\EventManager\SharedEventManager $ev
     */
    abstract public function register(\Zend\EventManager\SharedEventManager $ev);
}