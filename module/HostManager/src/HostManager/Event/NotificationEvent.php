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
use Application\Model\Mail;
use PM\Model\Users;
use PM\Model\Projects;
use PM\Model\Tasks;

/**
 * HostManager - Notification Events
 *
 * @package 	Events
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/src/HostManager/Event/NotificationEvent.php
 */
class NotificationEvent extends PMNotificationEvent
{   
	public function __construct( Mail $mail, Users $users, Projects $project, Tasks $task, $identity = null)
	{
		parent::__construct($mail, $users, $project, $task, $identity);
		$this->email_view_path = $this->mail->getModulePath(__DIR__).'/view/emails';
	}
	
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
    	$invite_id = $event->getParam('invite_id');
    	$invite = $event->getTarget();
    	$invite_data = $invite->getInvite(array('id' => $invite_id));
    	print_r($invite_data);
    	echo $invite_id;
    	exit;
    	$this->mail->addTo($data['email'], $data['first_name'].' '.$data['last_name']);
    	$this->mail->setViewDir($this->email_view_path);
    	$this->mail->setEmailView('account-invite', array('invite_data' => $invite_data));
    	$this->mail->setTranslationDomain('hm');
    	$this->mail->setSubject('account_invite_email_subject');
    	$this->mail->send();		
    }
}