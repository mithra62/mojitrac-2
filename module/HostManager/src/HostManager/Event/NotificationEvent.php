<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Event/NotificationEvent.php
 */

namespace HostManager\Event;

use PM\Event\NotificationEvent AS PMNotificationEvent;

 /**
 * PM - Notification Events
 *
 * @package 	Events
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/src/HostManager/Event/NotificationEvent.php
 */
class NotificationEvent extends PMNotificationEvent
{   
    /**
     * The hooks used for the Event
     * @var array
     */
    private $hooks = array(
    	'task.update.pre' => 'sendTaskUpdate',
    	'task.assign.pre' => 'sendTaskAssign',
    	'project.removeteammember.pre' => 'sendRemoveFromProjectTeam',
    	'project.addteam.post' => 'sendAddProjectTeam',
    	'file.add.post' => 'sendFileAdd',
    	'invite.add.post' => 'sendInviteAdd',
    );
    
    /**
     * We disable this so we don't send on user creation
     * @param \Zend\EventManager\Event $event
     */
    public function sendUserAdd(\Zend\EventManager\Event $event)
    {
    	  	
    }
    
    /**
     * Send an invite email to the user 
     * @param \Zend\EventManager\Event $event
     */
    public function sendInviteAdd(\Zend\EventManager\Event $event)
    {
    	$data = $event->getParam('data');
    	$user_id = $event->getParam('user_id');
    	$this->mail->addTo($data['email'], $data['first_name'].' '.$data['last_name']);
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('user-registration', array('user_data' => $data, 'user_id' => $user_id));
    	$this->mail->setTranslationDomain('pm');
    	$this->mail->setSubject('user_registration_email_subject');
    	$this->mail->send();		
    }
}