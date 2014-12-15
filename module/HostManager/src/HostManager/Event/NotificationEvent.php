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
    );
}