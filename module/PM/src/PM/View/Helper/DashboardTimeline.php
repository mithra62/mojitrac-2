<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/DashboardTimeline.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - Global Alerts View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/DashboardTimeline.php
 */
class DashboardTimeline extends BaseViewHelper
{
    	
	public function __invoke()
	{
	    $helperPluginManager = $this->getServiceLocator();
	    $serviceManager = $helperPluginManager->getServiceLocator();
	    $activity = $serviceManager->get('PM\Model\ActivityLog');

	    $filter = false;
		$identity = $this->getIdentity();
		$logs = $activity->getUsersProjectActivity($identity, $filter);
		if(count($logs) >= 1)
		{
			$count = 0;
			$html = array();
			foreach($logs AS $log)
			{
				$html[$count] = $log;
				$html[$count]['project_name'] = $log['project_name'];				
				$html[$count]['title'] = $this->determineTitle($log);
				$html[$count]['href'] = $this->determineURL($log);
				$html[$count]['image'] = $this->determineImage($log);
				$html[$count]['action_title'] = $this->determineActionTitle($log);
				$html[$count]['rel'] = $this->determineRel($log);
				$html[$count]['date'] = $log['date'];
				$count++; 
			}
			
			return $html;
		}
	}
	
	private function determineRel($data)
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{	
				case 'note_add':
				case 'note_remove':
				case 'note_update':
				case 'bookmark_add':
				case 'bookmark_remove':
				case 'bookmark_update':
				case 'file_revision_remove':
				case 'file_revision_add':
				case 'file_review_remove':
				case 'file_review_add':
					return 'facebox';
				break;

				case 'project_add':				
				case 'project_remove':			
				case 'project_update':
				case 'project_team_add':
					if(isset($data['project_name']) && $data['project_name'] != '')
					{
						return FALSE;
					}
					return 'facebox';
				break;
				
				case 'task_add':				
				case 'task_remove':				
				case 'task_update':
				case 'task_assigned':
					if(isset($data['task_name']) && $data['task_name'] != '')
					{
						return FALSE;
					}
					return 'facebox';
				break;
				
				case 'file_add':	
				case 'file_remove':	
				case 'file_update':
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return FALSE;
					} 
					return 'facebox';
				break;				
			}	
		}
	}
	
	private function determineActionTitle($data)
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{	
				case 'bookmark_add':
					return 'Bookmark Added';
				break;
				case 'bookmark_remove':
					return 'Bookmark Removed';
				break;
				case 'bookmark_update':
					return 'Bookmark Updated';
				break;
	
				case 'note_add':
					return 'Note Added';
				break;
				case 'note_remove':
					return 'Note  Removed';
				break;
				case 'note_update':
					return 'Note Updated';
				break;
	
				case 'project_add':
					return 'Project Added';
				break;					
				case 'project_remove':
					return 'Project Removed';
				break;					
				case 'project_update':
					return 'Project Updated';
				break;
				case 'project_team_add':
					return 'Project Team Updated';
				break;
				
				case 'task_add':
					return 'Task Added';
				break;					
				case 'task_remove':
					return 'Task Removed';
				break;					
				case 'task_update':
					return 'Task Updated';
				break;
				case 'task_assigned':
					return 'Task Assigned';
				break;
				
				case 'file_add':
					return 'File Added';
				break;					
				case 'file_remove':
					return 'File Removed';
				break;					
				case 'file_update':
					return 'File Updated';
				break;
				case 'file_revision_remove':
					return 'File Revision Removed';
				break;
				
				case 'file_revision_add':
					return 'File Revision Added';
				break;
				
				case 'file_review_remove':
					return 'File Review Removed';
				break;
				case 'file_review_add':
					return 'File Review Added';
				break;				
				
				default:
					return 'home_32.png';
				break;				
			}	
		}
	}
	
	private function determineURL($data)
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{
				case 'bookmark_add':
				case 'bookmark_remove':
				case 'bookmark_update':
					if(isset($data['bookmark_name']) && $data['bookmark_name'] != '')
					{
						return $this->view->url('bookmarks/view', array('bookmark_id' => $data['bookmark_id']));
					}
				break;
	
				case 'note_add':
				case 'note_remove':
				case 'note_update':
					if(isset($data['note_subject']) && $data['note_subject'] != '')
					{
						return $this->view->url('pm', array('module'=> 'pm', 'controller'=>'notes','action'=>'view', 'id' => $data['note_id']), null, TRUE);
					}
				break;
	
				case 'project_add':				
				case 'project_remove':			
				case 'project_update':
				case 'project_team_add':
					if(isset($data['project_name']) && $data['project_name'] != '')
					{
						return $this->view->url('projects/view', array('project_id' => $data['project_id']));
					}
				break;
				
				case 'task_add':				
				case 'task_remove':				
				case 'task_update':
				case 'task_assigned':
					if(isset($data['task_name']) && $data['task_name'] != '')
					{
						return $this->view->url('tasks/view', array('task_id' => $data['task_id']));
					}
				break;
				
				case 'file_add':	
				case 'file_remove':	
				case 'file_update':
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return $this->view->url('pm', array('module'=> 'pm', 'controller'=>'files','action'=>'view', 'id' => $data['file_id']), null, TRUE);
					} 
					$data['stuff'] = Zend_Json::decode($data['stuff']);

				break;
				
				case 'file_review_add':	
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return $this->view->url(array('module'=> 'pm', 'controller'=>'files','action'=>'view-review', 'id' => $data['file_review_id']), null, TRUE);
					} 
					$data['stuff'] = Zend_Json::decode($data['stuff']);
				break;	

				case 'file_revision_add':	
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return $this->view->url('pm', array('module'=> 'pm', 'controller'=>'files','action'=>'preview-revision', 'id' => $data['file_rev_id']), null, TRUE);
					} 
					$data['stuff'] = Zend_Json::decode($data['stuff']);
				break;					
			}
			return $this->view->url('pm', array('module'=> 'pm', 'controller'=>'activity','action'=>'view', 'id' => $data['id']), null, TRUE);
		}
	}
	
	private function determineImage($data)
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{
				case 'bookmark_add':
					return 'bookmark_add.png';
				break;					
				case 'bookmark_remove':
					return 'bookmark_remove.png';
				break;					
				case 'bookmark_update':
					return 'bookmark_edit.png';
				break;

				case 'note_add':
					return 'note_add.png';
				break;					
				case 'note_remove':
					return 'note_remove.png';
				break;					
				case 'note_update':
					return 'note_edit.png';
				break;

				case 'project_add':
				case 'project_remove':
				case 'project_update':
					return 'database_32.png';
				break;

				case 'project_team_add':
					return 'users_business_32.png';
				break;
				
				case 'task_add':
					return 'task_add.png';
				break;					
				case 'task_remove':
					return 'task_remove.png';
				break;					
				case 'task_update':
					return 'task_edit.png';
				break;
				
				case 'file_add':
					return 'file_add.png';
				break;					
				case 'file_remove':
					return 'file_remove.png';
				break;					
				case 'file_update':
					return 'file_edit.png';
				break;
				
				case 'file_review_add':
					return 'comment_add.png';
				break;
				case 'file_review_remove':
					return 'comment_remove.png';
				break;				
				
				case 'file_revision_add':
					return 'version_history_add.png';
				break;
				case 'file_revision_remove':
					return 'version_history_remove.png';
				break;
				
				case 'task_assigned':
					return 'history_32.png';
				break;
				
				default:
					return 'home_32.png';
				break;
			}
		}
	}
	
	private function determineTitle($data)
	{
		if(array_key_exists('type', $data))
		{
			switch($data['type'])
			{
				case 'bookmark_add':
				case 'bookmark_remove':
				case 'bookmark_update':
					
					if(isset($data['bookmark_name']) && $data['bookmark_name'] != '')
					{
						return $data['bookmark_name']; 
					} 
					$data['stuff'] = Zend_Json::decode($data['stuff']);
					if(isset($data['stuff']['name']) && $data['stuff']['name'] != '')
					{
						return $data['stuff']['name'];
					}
					
				break;
	
				case 'note_add':
				case 'note_remove':
				case 'note_update':
					if(isset($data['note_subject']) && $data['note_subject'] != '')
					{
						return $data['note_subject']; 
					} 
					$data['stuff'] = Zend_Json::decode($data['stuff']);
					if(isset($data['stuff']['subject']) && $data['stuff']['subject'] != '')
					{
						return $data['stuff']['subject'];
					}
				break;
	
				case 'project_add':
					return 'Project Added';
				break;					
				case 'project_remove':
					return 'Project Removed';
				break;					
				case 'project_update':
					return 'Project Updated';
				break;
				case 'project_team_add':
					return 'Project Team Updated';
				break;
				
				case 'task_add':	
				case 'task_remove':					
				case 'task_update':
				case 'task_assigned':
					
					if(isset($data['task_name']) && $data['task_name'] != '')
					{
						return $data['task_name']; 
					} 
					$data['stuff'] = \Zend\Json\Json::decode($data['stuff'], \Zend\Json\Json::TYPE_ARRAY);
					if(isset($data['stuff']['name']) && $data['stuff']['name'] != '')
					{
						return $data['stuff']['name'];
					}
					return 'Nothing here...';
					
				break;
				
				case 'file_add':	
				case 'file_remove':	
				case 'file_update':
				case 'file_revision_remove':
				case 'file_revision_add':
				case 'file_review_remove':
				case 'file_review_add':
					if(isset($data['file_name']) && $data['file_name'] != '')
					{
						return $data['file_name']; 
					} 
					$data['stuff'] = Zend_Json::decode($data['stuff']);
					if(isset($data['stuff']['file_name']) && $data['stuff']['file_name'] != '')
					{
						return $data['stuff']['file_name'];
					}
				break;				
				
				default:
					return 'home_32.png';
				break;
			}
		}
	}
}