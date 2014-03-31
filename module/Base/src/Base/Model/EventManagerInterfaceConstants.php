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

namespace Base\Model;

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
	const EventPasswordUpdatePre = 'password.update.pre';
	const EventPasswordUpdatePost = 'password.update.post';
	
	//context ids (company.X, project.X)
	const EventProjectUpdatePre = 'project.update.pre';
	const EventProjectUpdatePost = 'project.update.post';
	const EventProjectRemovePre = 'project.remove.pre';
	const EventProjectRemovePost = 'project.remove.post';
	const EventProjectAddPre = 'project.add.pre';
	const EventProjectAddPost = 'project.add.post';
	const EventProjectAddTeamPre = 'project.addteam.pre';
	const EventProjectAddTeamPost = 'project.addteam.post';
	const EventProjectRemoveTeamPre = 'project.removeteam.pre';
	const EventProjectRemoveTeamPost = 'project.removeteam.post';
	const EventProjectRemoveTeamMemberPre = 'project.removeteammember.pre';
	const EventProjectRemoveTeamMemberPost = 'project.removeteammember.post';
	
	const EventTaskUpdatePre = 'task.update.pre';
	const EventTaskUpdatePost = 'task.update.post';
	const EventTaskAddPre = 'task.add.pre';
	const EventTaskAddPost = 'task.add.post';
	const EventTaskRemovePre = 'task.remove.pre';
	const EventTaskRemovePost = 'task.remove.post';	
	const EventTaskAssignPre = 'task.assign.pre';
	const EventTaskAssignPost = 'task.assign.post';
	
	const EventCompanyUpdatePre = 'company.update.pre';
	const EventCompanyUpdatePost = 'company.update.post';
	const EventCompanyAddPre = 'company.add.pre';
	const EventCompanyAddPost = 'company.add.post';
	const EventCompanyRemovePre = 'company.remove.pre';
	const EventCompanyRemovePost = 'company.remove.post';
	
	const EventContactUpdatePre = 'company.contact.update.pre';
	const EventContactUpdatePost = 'company.contact.update.post';
	const EventContactAddPre = 'company.contact.add.pre';
	const EventContactAddPost = 'company.contact.add.post';
	const EventContactRemovePre = 'company.contact.remove.pre';
	const EventContactRemovePost = 'company.contact.remove.post';
	
	const EventFileUpdatePre = 'file.update.pre';
	const EventFileUpdatePost = 'file.update.post';
	const EventFileAddPre = 'file.add.pre';
	const EventFileAddPost = 'file.add.post';
	const EventFileRemovePre = 'file.remove.pre';
	const EventFileRemovePost = 'file.remove.post';	
	
	const EventNoteUpdatePre = 'note.update.pre';
	const EventNoteUpdatePost = 'note.update.post';
	const EventNoteAddPre = 'file.add.pre';
	const EventNoteAddPost = 'note.add.post';
	const EventNoteRemovePre = 'note.remove.pre';
	const EventNoteRemovePost = 'note.remove.post';		
	
	const EventBookmarkUpdatePre = 'bookmark.update.pre';
	const EventBookmarkUpdatePost = 'bookmark.update.post';
	const EventBookmarkAddPre = 'bookmark.add.pre';
	const EventBookmarkAddPost = 'bookmark.add.post';
	const EventBookmarkRemovePre = 'bookmark.remove.pre';
	const EventBookmarkRemovePost = 'bookmark.remove.post';	
	
	const EventUserAddPre = 'user.add.pre';
	const EventUserAddPost = 'user.add.post';
	const EventUserUpdatePre = 'user.update.pre';
	const EventUserUpdatePost = 'user.update.post';
	const EventUserRemovePre = 'user.remove.pre';
	const EventUserRemovePost = 'user.remove.post';
	const EventUserLogoutPre = 'user.logout.pre';
	const EventUserLogoutPost = 'user.logout.post';	
	
	const EventUserRoleAddPre = '';
	const EventUserRoleAddPost = '';
	const EventUserRoleUpdatePre = '';
	const EventUserRoleUpdatePost = '';
	const EventUserRoleRemovePre = '';
	const EventUserRoleRemovePost = '';	
	
	const EventSettingsUpdatePre = 'settings.update.pre';
	const EventSettingsUpdatePost = 'settings.update.post';
	
	const EventActivityLogAddPre = 'activitylog.add.pre';
	const EventActivityLogAddPost = 'activitylog.add.post';

}