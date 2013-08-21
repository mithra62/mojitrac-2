<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./moji/application/modules/pm/models/Admin.php
 */

 /**
 * PM - Admin Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/modules/pm/models/Admin.php
 */
class PM_Model_Admin extends Model_Abstract
{
	
	public function __construct()
	{
		
	}
	
	public function systemReset()
	{
		$project = new PM_Model_DbTable_Projects();
		$project->delete();
		
		$project_team = new PM_Model_DbTable_Projects_Teams();
		$project_team->delete();
		
		$task = new PM_Model_DbTable_Tasks;
		$task->delete();
		
		$task_assign = new PM_Model_DbTable_Task_Assignments;
		$task_assign->delete();
		
		$time = new PM_Model_DbTable_Times;
		$time->delete();
		
		$file = new PM_Model_DbTable_Files;
		$file->delete();
		
		$file_revision = new PM_Model_DbTable_File_Revisions();
		$file_revision->delete();
		
		$file_review = new PM_Model_DbTable_File_Reviews();
		$file_review->delete();
		
		$company = new PM_Model_DbTable_Companies;
		$company->delete();
		
		$contact = new PM_Model_DbTable_Contacts;
		$contact->delete();
		
		$user = new PM_Model_DbTable_Users;
		$user->delete("id != '".Zend_Auth::getInstance()->getIdentity()."'");
		
		$bookmark = new PM_Model_DbTable_Bookmarks;
		$bookmark->delete();
		
		$note = new PM_Model_DbTable_Notes;
		$note->delete();
		
		$assign = new PM_Model_DbTable_ActivityLog();
		$assign->delete();
		
		return TRUE;
	}
}