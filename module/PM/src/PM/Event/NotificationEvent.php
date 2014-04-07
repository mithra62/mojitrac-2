<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Event/NotificationEvent.php
 */

namespace PM\Event;

use Base\Event\BaseEvent;

 /**
 * PM - Notification Events
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Event/NotificationEvent.php
 */
class NotificationEvent extends BaseEvent
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
        'user.add.post' => 'sendUserAdd',
    );
    
    /**
     * The Notification Event
     * @param \Application\Model\Mail $mail
     * @param \Application\Model\Users $users
     * @param string $identity
     */
    public function __construct( \Application\Model\Mail $mail, \Application\Model\Users $users, $identity = null)
    {
        $this->mail = $mail;
        $this->identity = $identity;
        $this->users = $users;
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
    public function sendUserAdd(\Zend\EventManager\Event $event)
    {
    	$data = $event->getParam('data');
    	$user_id = $event->getParam('user_id');
    	$this->mail->addTo($data['email'], $data['first_name'].' '.$data['last_name']);
    	$this->mail->setViewDir($this->mail->getModulePath(__DIR__));
    	$this->mail->setEmailView('user-registration', array('user_data' => $data, 'user_id' => $user_id));
    	$this->mail->setSubject('user_registration_email_subject');
    	return $this->mail->send($mail->transport);    	
    }
}