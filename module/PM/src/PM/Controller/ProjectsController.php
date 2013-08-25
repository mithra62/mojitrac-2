<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/ProjectsController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
* PM - Projects Controller
*
* Routes the Project requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/ProjectsController.php
*/
class ProjectsController extends AbstractPmController
{

	/**
	 * Class preDispatch
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		parent::onDispatch( $e );
        parent::check_permission('view_projects');
        $this->layout()->setVariable('layout_style', 'single');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('sub_menu', 'projects');
        $this->layout()->setVariable('active_nav', 'projects');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'None');
		    
		return $e;
	}
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{
		$company_id = $this->params()->fromRoute('company_id');
		if($company_id)
		{
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				$company_id = $company_data = FALSE;
			}
		}
		
	    $projects = $this->getServiceLocator()->get('PM\Model\Projects'); 
		//$view = $this->_getParam("view",FALSE);
		$this->layout()->setVariable('active_sub', $view);
		$this->layout()->setVariable('project_filter', ($view !== FALSE ? (string)$view : FALSE));
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
	        	$project_data = $projects->getAllProjects(FALSE);
	        	$this->layout()->setVariable('projects', $project_data);
	        }
	        else
	        {
				$user = new PM_Model_Users(new PM_Model_DbTable_Users);
				$this->view->projects = $user->getAssignedProjects($this->identity);	    		
	        }
		}
		
		return array('projects' => $project_data);
	}
	
	/**
	 * Project View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('project_id');
		if (!$id) 
		{
			$this->_helper->redirector('index','projects');
			exit;
		}
		
		$view = array();
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$view['project'] = $project->getProjectById($id);
		if(!$view['project'])
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
	
		$view['proj_team'] = $proj_team;
		$view['user_is_on_team'] = $on_team;
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$times = $this->getServiceLocator()->get('PM\Model\Times');

		if($this->perm->check($this->identity, 'view_tasks'))
		{		
			$view['tasks'] = $task->getTasksByProjectId($id, null, array('status' => 6));
		}

		if($this->perm->check($this->identity, 'view_files'))
		{		
			$view['files'] = $file->getFilesByProjectId($id);
		}
		
		if($this->perm->check($this->identity, 'view_time'))
		{	
			$not = array('bill_status' => 'paid');
			$view['times'] = $times->getTimesByProjectId($id, null, $not);
			$view['hours'] = $times->getTotalTimesByProjectId($id);	
			
			$view['estimated_time'] = $task->getProjectEstimatedTime($id);
		}		

		$bookmarks = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$view['bookmarks'] = $bookmarks->getBookmarksByProjectId($id);	

		$notes = $this->getServiceLocator()->get('PM\Model\Notes');
		$view['notes'] = $notes->getNotesByProjectId($id);	

		$this->layout()->setVariable('active_sub', $view['project']['status']);
		$view['identity'] = $this->identity;
		///$this->view->headTitle('Viewing Project: '. $this->view->project['name'], 'PREPEND');
		$view['id'] = $id;
		
		return $view;
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
		$company_id = $this->params()->fromRoute('company_id');
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
	
	public function removeAction()
	{
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		$id = $this->params()->fromRoute('project_id');
		$confirm = $this->params()->fromPost('confirm');
		$fail = $this->params()->fromPost('fail');
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','projects');
    		exit;
    	}
    	
    	$project_data = $project->getProjectById($id);
    	$view['project'] = $project_data;
    	if(!$view['project'])
    	{
			$this->_helper->redirector('index','projects');
			exit;
    	}

    	if($fail)
    	{
    		return $this->redirect()->toRoute('projects/view', array('project_id' => $id));
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
    	
    	$view['task_count'] = $project->getTaskCount($id);
    	$view['file_count'] = $project->getFileCount($id);
    	
		//$this->view->headTitle('Delete Project: '. $this->view->project['name'], 'PREPEND');
		$view['id'] = $id;  

		return $view;
	}
	
	/**
	 * The Manage Team Page
	 * @return void
	 */
	public function manageTeamAction()
	{
		
		$id = $this->params()->fromRoute('project_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('projects');
		}
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
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
		
		$view['id'] = $id;
		$view['project'] = $project_data;

		$view['proj_team'] = $proj_team;
		$users = $this->getServiceLocator()->get('Application\Model\User');
		$view['users'] = $users->getAllUsers('d');
		
		return $view;
	}
}