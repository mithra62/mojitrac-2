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

use Base\Event\BaseEvent;

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
    );
    
    /**
     * The Notification Event
     * @param \Application\Model\Mail $mail
     * @param \Application\Model\Users $users
     * @param string $identity
     */
    public function __construct($identity = null )
    {
        $this->identity = $identity;
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
     * Sends the user registration notification
     * @param \Zend\EventManager\Event $event
     */
    public function selectPre(\Zend\EventManager\Event $event)
    {
    	$sql = $event->getParam('sql');

    }
}