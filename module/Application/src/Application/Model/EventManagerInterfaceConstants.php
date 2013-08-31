<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./moji/application/models/EventManagerInterfaceConstants.php
 */

namespace Application\Model;

use Zend\EventManager\EventManagerAwareInterface;

/**
 * Event Manager Interaface Constants
 *
 * Contains all the Event Hook Names used within the Moji Models
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/models/EventManagerInterfaceConstants.php
 */
interface EventManagerInterfaceConstants extends EventManagerAwareInterface
{
	const EventInitPasswordUpdate = 'init.password.update';
	const EventPostPasswordUpdate = 'post.password.update';
	
	const EventInitProjectUpdate = 'init.project.update';
	const EventPostProjectUpdate = 'post.project.update';
	const EventInitProjectRemove = 'init.project.remove';
	const EventPostProjectRemove = 'post.project.remove';
	const EventInitProjectAdd = 'init.project.add';
	const EventPostProjectAdd = 'post.project.add';
	
	const EventInitTaskUpdate = 'init.task.update';
	const EventPostTaskUpdate = 'post.task.update';
	const EventInitTaskAdd = 'init.task.add';
	const EventPostTaskAdd = 'post.task.add';
	const EventInitTaskRemove = 'init.task.remove';
	const EventPostTaskRemove = 'post.task.remove';	
	
	const EventInitCompanyUpdate = 'init.company.update';
	const EventPostCompanyUpdate = 'post.company.update';
	const EventInitCompanyAdd = 'init.company.add';
	const EventPostCompanyAdd = 'post.company.add';
	const EventInitCompanyRemove = 'init.company.remove';
	const EventPostCompanyRemove = 'post.company.remove';	
	
	const EventInitFileUpdate = 'init.file.update';
	const EventPostFileUpdate = 'post.file.update';
	const EventInitFileAdd = 'init.file.add';
	const EventPostFileAdd = 'post.file.add';
	const EventInitFileRemove = 'init.file.remove';
	const EventPostFileRemove = 'post.file.remove';	
	
	const EventInitNoteUpdate = 'init.note.update';
	const EventPostNoteUpdate = 'post.note.update';
	const EventInitNoteAdd = 'init.file.add';
	const EventPostNoteAdd = 'post.note.add';
	const EventInitNoteRemove = 'init.note.remove';
	const EventPostNoteRemove = 'post.note.remove';		
	
	const EventInitBookmarkUpdate = 'init.bookmark.update';
	const EventPostBookmarkUpdate = 'post.bookmark.update';
	const EventInitBookmarkAdd = 'init.bookmark.add';
	const EventPostBookmarkAdd = 'post.bookmark.add';
	const EventInitBookmarkRemove = 'init.bookmark.remove';
	const EventPostBookmarkRemove = 'post.bookmark.remove';	
}