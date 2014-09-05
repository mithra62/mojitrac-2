<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/src/Freshbooks/Event/SettingsEvent.php
 */

namespace Freshbooks\Event;

use Base\Event\BaseEvent;

/**
 * Freshbooks - Test/Example Event
 * 
 * Provides a working example of an Event for developers.
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Freshbooks/src/Freshbooks/Event/SettingsEvent.php
 */
class SettingsEvent extends BaseEvent
{	
	/**
	 * The default settings to append to the Moji settings array
	 * @var array
	 */	
	private $default_settings = array(
		'freshbooks_api_url' => '',
		'freshbooks_auth_token' => ''
	);
	
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
        'settings.defaults.set.pre' => 'modSettingsDefaults'
    );
    
    public function __construct( )
    {
    	
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
     * Merges our settings array into the MojiTrac Settings array 
     * @param \Zend\EventManager\Event $event
     * @return array
     */
	public function modSettingsDefaults(\Zend\EventManager\Event $event)
	{
		$defaults = $event->getParam('defaults');
		$defaults = array_merge($defaults, $this->default_settings);
		$event->setParam('defaults', $defaults);
		return $defaults;
		
	}
}