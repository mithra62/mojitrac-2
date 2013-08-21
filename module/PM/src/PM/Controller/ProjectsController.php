<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/ProjectsController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Projects Controller
*
* Routes the Projects requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/ProjectsController.php
*/
class Pm_ProjectsController extends PM_Abstract
{

	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{ 
		parent::preDispatch();
        parent::check_permission('view_projects');
        $this->view->headTitle('Projects', 'PREPEND');
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
		$company_id = $this->_request->getParam('company', FALSE);
		if($company_id)
		{
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				$company_id = $company_data = FALSE;
			}
		}
		
	    $projects = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$view = $this->_getParam("view",FALSE);
		$this->view->active_sub = $view;
		$this->view->project_filter = ($view !== FALSE ? (string)$view : FALSE);
		if($company_id)
		{
			$this->view->projects = $projects->getProjectsByCompanyId($company_id);
			$this->view->headTitle('Projects For: '. $company_data['name'], 'PREPEND');
       		$this->view->sub_menu = 'company';
       		$this->view->active_sub = 'projects';
        	$this->view->active_nav = 'companies';
        	$this->view->company_id = $company_id;
        	$this->view->company_data = $company_data;
			
		}
		else
		{
			if($this->perm->check($this->identity, 'manage_projects'))
	        {
	        	$this->view->projects = $projects->getAllProjects($view);
	        }
	        else
	        {
				$user = new PM_Model_Users(new PM_Model_DbTable_Users);
				$this->view->projects = $user->getAssignedProjects($this->identity);	    		
	        }
		}
	}
	
	/**
	 * Project View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->_helper->redirector('index','projects');
			exit;
		}
		
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$this->view->project = $project->getProjectById($id);
		if(!$this->view->project)
		{
			$this->_helper->redirector('index','projects');
			exit;
		}
		
		$proj_team = $project->getProjectTeamMembers($id);
		$on_team = $project->isUserOnProjectTeam($this->identity, $id, $proj_team);
		if(!$on_team && !$this->perm->check($this->identity, 'manage_projects'))
		{
			$this->_helper->redirector('index','projects');
			exit;			
		}
		
		$this->view->proj_team = $proj_team;
		$this->view->user_is_on_team = $on_team;
		$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		$times = new PM_Model_Times;

		if($this->perm->check($this->identity, 'view_tasks'))
		{		
			$this->view->tasks = $task->getTasksByProjectId($id, null, array('status' => 6));
		}

		if($this->perm->check($this->identity, 'view_files'))
		{		
			$this->view->files = $file->getFilesByProjectId($id);
		}
		
		if($this->perm->check($this->identity, 'view_time'))
		{	
			$not = array('bill_status' => 'paid');
			$this->view->times = $times->getTimesByProjectId($id, null, $not);
			$this->view->hours = $times->getTotalTimesByProjectId($id);	
			
			$this->view->estimated_time = $task->getProjectEstimatedTime($id);
		}		

		$bookmarks = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
		$this->view->bookmarks = $bookmarks->getBookmarksByProjectId($id);	

		$notes = new PM_Model_Notes;
		$this->view->notes = $notes->getNotesByProjectId($id);	

		$this->view->active_sub = $this->view->project['status'];
		$this->view->identity = $this->identity;
		$this->view->headTitle('Viewing Project: '. $this->view->project['name'], 'PREPEND');
		$this->view->id = $id;
	}
	
	/**
	 * Project Edit Page
	 * @return void
	 */
	public function editAction()
	{
        		
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->_helper->redirector('index','projects');
			exit;
		}		
		
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$project_data = $project->getProjectById($id);
		if (!$project_data) 
		{
			$this->_helper->redirector('index','projects');
			exit;
		}
		
		if(!$this->perm->check($this->identity, 'manage_projects'))
        {
        	$this->_helper->redirector('view', 'projects', 'pm', array('id'=>$id));
        	exit;
        }		
		
		$form = $project->getProjectForm(array(
            'action' => '/pm/projects/edit/',
            'method' => 'post',
        ), array('id' => $id));
        
        $this->view->id = $id;
        
        if($project_data['start_date'] == '0000-00-00')
        {
        	$project_data['start_date'] = '';
        }
        
        if($project_data['end_date'] == '0000-00-00')
        {
        	$project_data['end_date'] = '';
        }
        
        $form->populate($project_data);	
        
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) 
            {
            	if($project->updateProject($formData, $id))
	            {
	            	PM_Model_ActivityLog::logProjectUpdate($formData, $id, $this->identity);
			    	$this->_flashMessenger->addMessage('Project updated!');
					$this->_helper->redirector('view', 'projects', 'pm', array('id' => $id));    
					        		
            	} 
            	else 
            	{
            		$this->view->errors = array('Couldn\'t update company...');
            		$form->populate($formData);
            	}
                
            } 
            else 
            {
            	$this->view->errors = array('Please fix the errors below.');
                $form->populate($formData);
            }
            
	    }
	    
	    Zend_Registry::set('pm_activity_filter', array('project_id' => $id));
	    $this->view->project_data = $project_data;
	    $this->view->active_sub = $project_data['status'];
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';	
		$this->view->headTitle('Edit Project', 'PREPEND');     	
	}
	
	/**
	 * Project Add Page
	 * @return void
	 */
	public function addAction()
	{
		$company_id = $this->_request->getParam('company', FALSE);
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$form = $project->getProjectForm(array(
            'action' => '/pm/projects/add',
            'method' => 'post',
        ));
        
        if($company_id)
        {
        	$form->populate(array('company_id' => $company_id));
        }        
		
		 if ($this->getRequest()->isPost()) 
		 {
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				$formData['creator'] = $this->identity;
				if($id = $project->addProject($formData))
				{
					PM_Model_ActivityLog::logProjectAdd($formData, $id, $this->identity);
					if($project->addProjectTeamMember($this->identity, $id))
					{
						PM_Model_ActivityLog::logProjectTeamAdd(array('first_user'), $id, $this->identity);
					}
					
					if(is_numeric($formData['company_id']))
					{
						$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
						$company->updateCompanyProjectCount($formData['company_id']);
					}
					
			    	$this->_flashMessenger->addMessage('Project Added!');
					$this->_helper->redirector('view','projects', 'pm', array('id' => $id));					
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
		$this->view->title = FALSE;
		$this->view->headTitle('Add Project', 'PREPEND');

		$this->view->form = $form;
	}
	
	function removeAction()
	{
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','projects');
    		exit;
    	}
    	
    	$project_data = $project->getProjectById($id);
    	$this->view->project = $project_data;
    	if(!$this->view->project)
    	{
			$this->_helper->redirector('index','projects');
			exit;
    	}

    	if($fail)
    	{
			$this->_helper->redirector('view','projects', 'pm', array('id' => $id));
			exit;   		
    	}
    	
    	if($confirm)
    	{
    	   	if($project->removeProject($id))
    		{	
    			$project->removeProjectTeam($id);
    			PM_Model_ActivityLog::logProjectRemove($project_data, $id, $this->identity);
				$this->_flashMessenger->addMessage('Project Removed');
				$this->_helper->redirector('index','projects');
				exit;
				
    		}
    	}
    	
    	$this->view->task_count = $project->getTaskCount($id);
    	$this->view->file_count = $project->getFileCount($id);
    	
		$this->view->headTitle('Delete Project: '. $this->view->project['name'], 'PREPEND');
		$this->view->id = $id;    	
	}
	
	/**
	 * The Manage Team Page
	 * @return void
	 */
	public function manageTeamAction()
	{
		
		$id = $this->_request->getParam('project', FALSE);
		if (!$id) 
		{
			$this->_helper->redirector('index','projects');
		}
		
		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
		$project_data = $project->getProjectById($id);
		if(!$project_data)
		{
			$this->_helper->redirector('index','projects');
		}
		
		$proj_team = $project->getProjectTeamMemberIds($id);
		if ($this->getRequest()->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			$errors = FALSE;
			if(array_key_exists('proj_member', $formData))
			{
				foreach($formData['proj_member'] AS $key => $value) //add users to the team
				{
					if(!in_array($key, $proj_team)) //user is not on the team yet; add them
					{
						if($project->addProjectTeamMember($key, $id))
						{
							PM_Model_ActivityLog::logProjectTeamAdd($formData['proj_member'], $id, $this->identity);
							$noti = new PM_Model_Notifications();
							$noti->addToProjectTeam($key, $project_data);							
						}
					}
				}
			}
			
			if(array_key_exists('proj_member', $formData))
			{
				foreach($proj_team AS $removed)
				{	
					if(!array_key_exists($removed, $formData['proj_member']))
					{	
						if($project->removeProjectTeamMember($removed, $id))
						{
							PM_Model_ActivityLog::logProjectTeamRemove($proj_team, $id, $this->identity);
							$noti = new PM_Model_Notifications();
							$noti->removeFromProjectTeam($removed, $project_data);							
						}
					}
				}
			}
			
			if(!$errors)
			{
				$this->_flashMessenger->addMessage('Project Team Modified!');
				$this->_helper->redirector('view','projects', 'pm', array('id' => $id));
				exit;
			}
		}
		
		$this->view->id = $id;
		$this->view->project = $project_data;

		$this->view->proj_team = $proj_team;
		$users = new PM_Model_Users(new PM_Model_DbTable_Users);
		$this->view->users = $users->getAllUsers('d');
		$this->view->title = FALSE;
	}
}