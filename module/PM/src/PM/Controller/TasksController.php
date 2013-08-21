<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/TasksController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Tasks Controller
*
* Handles tasks routing
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/TasksController.php
*/
class Pm_TasksController extends PM_Abstract
{

	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{ 
		parent::preDispatch();
        parent::check_permission('view_tasks');
        $this->view->headTitle('Tasks', 'PREPEND');
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'projects';
        $this->view->active_nav = 'projects';
        $this->view->sub_menu_options = PM_Model_Options_Projects::status();
        $this->view->uri = $this->_request->getPathInfo();
		$this->view->active_sub = 'None';
		$this->view->title = FALSE;          
	}
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{
		$project_id = $this->_request->getParam('project', FALSE);
		if($project_id)
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			if(!$project->isUserOnProjectTeam($this->identity, $project_id) && !$this->perm->check($this->identity, 'manage_projects'))
			{
				$this->_helper->redirector('index','index');
				exit;				
			}
			
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
				$this->_helper->redirector('index','tasks');
				exit;
			}
			
			$tasks = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$view = $this->_getParam("view",FALSE);
			$this->view->active_sub = $view;
		    $this->view->tasks = $tasks->getTasksByProjectId($project_id);
		    $this->view->project_data = $project_data;			
		}
		else
		{
		    $this->_helper->redirector('index','index');
			exit;
		}
	}
	
	/**
	 * Company View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->_helper->redirector('index','tasks');
			exit;
		}
		
		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		$task_data = $task->getTaskById($id);
		if($task_data['assigned_to'] == $this->identity)
		{
			$this->view->assigned_to = TRUE;
		}
		
		$this->view->task = $task_data;
		if (!$task_data) 
		{
			$this->_helper->redirector('index','index');
			exit;
		}
		
		if(!$this->perm->check($this->identity, 'view_tasks'))
		{
			$this->_helper->redirector('view','projects','pm', array('id' => $task_data['project_id']));
			exit;			
		}
		
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			$this->_helper->redirector('index','index');
			exit;				
		}		
		
		$this->view->assignment_history = $task->getTaskAssignments($id);

		if($this->perm->check($this->identity, 'view_files'))
		{
			$file = new PM_Model_Files(new PM_Model_DbTable_Files);
			$this->view->files = $file->getFilesByTaskId($id);
		}

		if($this->perm->check($this->identity, 'view_time'))
		{
			$times = new PM_Model_Times;
			$this->view->times = $times->getTimesByTaskId($id);
			$this->view->hours = $times->getTotalTimesByTaskId($id);
		}
		
		$bookmarks = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
		$this->view->bookmarks = $bookmarks->getBookmarksByTaskId($id);	

		$notes = new PM_Model_Notes;
		$this->view->notes = $notes->getNotesByTaskId($id);		
		
		$this->view->title = FALSE;
		$this->view->headTitle('Viewing Task: '. $this->view->task['name'], 'PREPEND');
		$this->view->id = $id;
	}
	
	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->_helper->redirector('index','tasks');
			exit;
		}
		
		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		$task_data = $task->getTaskById($id);
		if (!$task_data) 
		{
			$this->_helper->redirector('index','tasks');
			exit;
		}

		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			$this->_helper->redirector('view','tasks', 'pm', array('id' => $id));
			exit;				
		}
				
        $this->view->id = $id;

        $project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
        $this->view->project_data = $project->getProjectById($task_data['project_id']);
        
        $task_data = $task->parseTaskDates($task_data);
        
		$form = $task->getTaskForm($task_data['project_id'], array(
            'action' => '/pm/tasks/edit/',
            'method' => 'post',
        ), array('id' => $id));

	    if($task_data['start_date'] == '0000-00-00')
        {
        	$task_data['start_date'] = '';
        }
        
        if($task_data['end_date'] == '0000-00-00')
        {
        	$task_data['end_date'] = '';
        }
        
        $form->populate($task_data);	
        
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $formData['project_id'] = $task_data['project_id'];
            $formData['project_name'] = $task_data['project_name'];
            if ($form->isValid($formData)) 
            {
            	$noti = new PM_Model_Notifications;
            	if($task->updateTask($formData, $id))
	            {	            	
	            	PM_Model_ActivityLog::logTaskUpdate($formData, $id, $formData['project_id'], $this->identity);
					if($formData['assigned_to'] != $task_data['assigned_to'])
	            	{
	            		PM_Model_ActivityLog::logTaskAssignment($formData, $id, $formData['project_id'], $this->identity);
	            		$assign_desc = (isset($formData['assign_comment']) ? $formData['assign_comment'] : null);
	            		$task->logTaskAssignment($id, $formData['assigned_to'], $this->identity, $assign_desc);
	            		if($formData['assigned_to'] != 0)
	            		{
	            			$noti->sendTaskAssignment($formData);
	            		}
	            	}
	            		            	
	            	if($formData['status'] != $task_data['status'] && ($formData['priority'] == $task_data['priority']))
	            	{
	            		$noti->sendTaskStatusChange($formData);	            		
	            	}
	            	
	            	if($formData['priority'] != $task_data['priority'])
	            	{
	            		$noti->sendTaskPriorityChange($formData);	            		
	            	}
	            	
	            	if($formData['end_date'] != $task_data['end_date'])
	            	{
	            		//$noti = new PM_Model_Notifications;
	            		//$noti->sendTaskEndDateChange($task_data);	            		
	            	}

	            	$task->updateCompanyId($id, FALSE, $formData['project_id']);	            	
			    	$this->_flashMessenger->addMessage('Task updated!');
					$this->_helper->redirector('view','tasks', 'pm', array('id' => $id));  
					exit;      		
            	} 
            	else 
            	{
            		$this->view->errors = array('Couldn\'t update task...');
            		$form->populate($formData);
            	}
                
            } 
            else 
            {
            	$this->view->errors = array('Please fix the errors below.');
                $form->populate($formData);
            }
	    }
	    
	    Zend_Registry::set('pm_activity_filter', array('project_id' => $task_data['project_id']));
	    $this->view->task_data = $task_data;
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		    
		$this->view->headTitle('Edit Task', 'PREPEND');     	
	}
	
	public function updateProgressAction()
	{
		if (!$this->getRequest()->isPost()) {
			exit;
		}
				
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			exit;
		}
		
		$progress = $this->_request->getParam('progress', FALSE);
		if (!$progress || $progress > 100 || $progress < 0) 
		{
			exit;
		}		
		
		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		$task_data = $task->getTaskById($id);
		if (!$task_data) 
		{
			exit;
		}
		
		if($task_data['assigned_to'] != $this->identity)
		{
			exit;
		}		

		$task->updateProgress($id, $progress);
		exit;
	}
	
	/**
	 * Task Add Page
	 * @return void
	 */
	public function addAction()
	{
		
		$project = $this->_request->getParam('project', FALSE);
		$company = $this->_request->getParam('company', FALSE);
		if(!$company && !$project)
		{
			$this->_helper->redirector('index','tasks');
			exit;
		}
		
		if($project)
		{
			$projects = new PM_Model_Projects(new PM_Model_DbTable_Projects);			
			$this->view->project_data = $projects->getProjectById($project);
			if(!$this->view->project_data)
			{
				$this->_helper->redirector('index','tasks');
				exit;				
			}
			
		}
				
		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		$form = $task->getTaskForm($project);
		
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				$formData['creator'] = $this->identity;
				if((is_numeric($formData['start_hour']) && $formData['start_hour'] <= 24) 
				&& (is_numeric($formData['start_minute']) && $formData['start_minute'] <= 60))
				{	
					$formData['start_date'] = $formData['start_date'].' '.$formData['start_hour'].':'.$formData['start_minute'];
				}
				
				if((is_numeric($formData['end_hour']) && $formData['end_hour'] <= 24) 
				&& (is_numeric($formData['end_minute']) && $formData['end_minute'] <= 60))
				{	
					$formData['end_date'] = $formData['end_date'].' '.$formData['end_hour'].':'.$formData['end_minute'];
				}
				
				if($id = $task->addTask($formData))
				{
					PM_Model_ActivityLog::logTaskAdd($formData, $id, $formData['project_id'], $this->identity);					
				    if($formData['assigned_to'] != 0)
            		{
            			$formData['id'] = $id;
            			$task->logTaskAssignment($id, $formData['assigned_to'], $this->identity);            			
            			PM_Model_ActivityLog::logTaskAssignment($formData, $id, $formData['project_id'], $this->identity);
            			
            			$noti = new PM_Model_Notifications;            			
            			$noti->sendTaskAssignment($formData);
            		}
            							
					$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
					$project->updateProjectTaskCount($formData['project_id']);
					$task->updateCompanyId($id, FALSE, $formData['project_id']);

			    	$this->_flashMessenger->addMessage('Task Added!');
					$this->_helper->redirector('view','tasks', 'pm', array('id' => $id));
										
					exit;
				}
			} 
			else 
			{
				$this->view->errors = array('Please fix the errors below.');
			}
		 }
		
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		
		$this->view->headTitle('Add Task', 'PREPEND');

		$this->view->form = $form;
	}
	
	function removeAction()
	{
		
		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','projects');
    		exit;
    	}
    	
    	$task_data = $task->getTaskById($id);
    	$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			$this->_helper->redirector('view','tasks', 'pm', array('id' => $id));
			exit;				
		}
			    	
    	$this->view->task = $task_data;
    	if(!$this->view->task)
    	{
			$this->_helper->redirector('index','projects');
			exit;
    	}

    	if($fail)
    	{
			$this->_helper->redirector('view','tasks', 'pm', array('id' => $id));
			exit;   		
    	}
    	
    	if($confirm)
    	{
    	   	if($task->removeTask($id))
    		{	
    			PM_Model_ActivityLog::logTaskRemove($task_data, $id, $task_data['project_id'], $this->identity);
				$this->_flashMessenger->addMessage('Task Removed');
				$this->_helper->redirector('view','projects', 'pm', array('id' => $task_data['project_id']));
				exit;
				
    		}
    	}
    	
    	$this->view->file_count = $task->getFileCount($id);
    	
		$this->view->headTitle('Delete Task: '. $this->view->task['name'], 'PREPEND');
		$this->view->id = $id;    	
	}	
	
    public function icalAction()
    {
    	$this->view->layout()->disableLayout();
		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		$id = $this->_request->getParam('id', FALSE);
		
        if(!$id)
    	{
    		$this->_helper->redirector('index','projects');
    		exit;
    	}
    	
    	$task_data = $task->getTaskById($id);
    	$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			$this->_helper->redirector('view','tasks', 'pm', array('id' => $id));
			exit;				
		}
			
    	$ical = new PM_Model_Ical;
    	$ical->event_id = 'SSPCAEvent_Moji_task_'.$id;
    	$ical->desc = $task_data['description'];
    	$ical->filename = $ical->event_id.'.ics';
    	$ical->download();
    	
    	//print_r($task_data);
    	exit;
    	
    }	
}