<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/TasksController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Tasks Controller
*
* Routes the Tasks requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/TasksController.php
*/
class TasksController extends AbstractPmController
{

	/**
	 * Class preDispatch
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		parent::onDispatch( $e );
        parent::check_permission('view_tasks');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('sub_menu', 'projects');
        $this->layout()->setVariable('active_nav', 'projects');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'None');         
	}
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{
		$project_id = $this->params()->fromRoute('project_id');
		if(!$project_id)
		{
		    return $this->redirect()->toRoute('pm');
		}
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $project_id) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('pm');			
		}
		
		$project_data = $project->getProjectById($project_id);
		if(!$project_data)
		{
			return $this->redirect()->toRoute('projects');
		}
		
		$tasks = $this->getServiceLocator()->get('PM\Model\Tasks');
		//$this->view->active_sub = $view;
	    $view['tasks'] = $tasks->getTasksByProjectId($project_id);
	    $view['project_data'] = $project_data;	

	    return $view;

	}
	
	/**
	 * Company View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('task_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('tasks');
		}
		
		$view = array();
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$task_data = $task->getTaskById($id);
		if($task_data['assigned_to'] == $this->identity)
		{
			$view['assigned_to'] = TRUE;
		}
		
		$view['task'] = $task_data;
		if (!$task_data) 
		{
			return $this->redirect()->toRoute('pm');
		}
		
		if(!$this->perm->check($this->identity, 'view_tasks'))
		{
			return $this->redirect()->toRoute('projects/view', array('project_id' => $task_data['project_id']));
		}
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('pm');				
		}		
		
		$view['assignment_history'] = $task->getTaskAssignments($id);
		if($this->perm->check($this->identity, 'view_files'))
		{
			$file = $this->getServiceLocator()->get('PM\Model\Files');
			$view['files'] = $file->getFilesByTaskId($id);
		}

		if($this->perm->check($this->identity, 'view_time'))
		{
			$times = $this->getServiceLocator()->get('PM\Model\Times');
			$view['times'] = $times->getTimesByTaskId($id);
			$view['hours'] = $times->getTotalTimesByTaskId($id);
		}
		
		$bookmarks = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$view['bookmarks'] = $bookmarks->getBookmarksByTaskId($id);	

		$notes = $this->getServiceLocator()->get('PM\Model\Notes');
		$view['notes'] = $notes->getNotesByTaskId($id);		
		
		//$this->view->title = FALSE;
		//$this->view->headTitle('Viewing Task: '. $this->view->task['name'], 'PREPEND');
		$view['id'] = $id;
		return $view;
	}
	
	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('task_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('pm');
		}
		
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$task_data = $task->getTaskById($id);
		if (!$task_data) 
		{
			return $this->redirect()->toRoute('pm');
		}

		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
		}
				
        $view['id'] = $id;
        $view['project_data'] = $project->getProjectById($task_data['project_id']);
        
        $task_data = $task->parseTaskDates($task_data);
        
		$form = $this->getServiceLocator()->get('PM\Form\TaskForm');
		$form->setup($task_data['project_id']);
	    if($task_data['start_date'] == '0000-00-00')
        {
        	$task_data['start_date'] = '';
        }
        
        if($task_data['end_date'] == '0000-00-00')
        {
        	$task_data['end_date'] = '';
        }
        
        $form->setData($task_data);	
        
        $view['form'] = $form;
        
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $formData['project_id'] = $task_data['project_id'];
            $formData['project_name'] = $task_data['project_name'];
            
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($task->getInputFilter());
            $form->setData($formData);
                        
            if ($form->isValid($formData)) 
            {
                $formData['creator'] = $this->identity;
            	if($task->updateTask($formData->toArray(), $id))
	            {		            	
	            	if($formData['status'] != $task_data['status'] && ($formData['priority'] == $task_data['priority']))
	            	{
	            	    //todo
	            		//$noti->sendTaskStatusChange($formData);	            		
	            	}
	            	
	            	if($formData['priority'] != $task_data['priority'])
	            	{
	            	    //todo
	            		//$noti->sendTaskPriorityChange($formData);	            		
	            	}
	            	
	            	if($formData['end_date'] != $task_data['end_date'])
	            	{
	            		//$noti = new PM_Model_Notifications;
	            		//$noti->sendTaskEndDateChange($task_data);	            		
	            	}

	            	//$task->updateCompanyId($id, FALSE, $formData['project_id']);	            	
			    	$this->flashMessenger()->addMessage('Task updated!');
					return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
            	} 
            	else 
            	{
            		$view['errors'] = array('Couldn\'t update task...');
            		$form->setData($formData);
            	}
                
            } 
            else 
            {
            	$view['errors'] = array('Please fix the errors below.');
                $form->setData($formData);
            }
	    }
	    
	    $view['task_data'] = $task_data;
        $this->layout()->setVariable('layout_style', 'left');
		return $view;
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
		
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
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
		
		$project = $this->params()->fromRoute('project_id');
		if(!$project)
		{
			return $this->redirect()->toRoute('pm');
		}
		
		if($project)
		{
			$projects = $this->getServiceLocator()->get('PM\Model\Projects');		
			$view['project_data'] = $projects->getProjectById($project);
			if(!$view['project_data'])
			{
				return $this->redirect()->toRoute('pm');			
			}
			
		}
				
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$form = $this->getServiceLocator()->get('PM\Form\TaskForm');
		$form->setup($project);
		$form->setData(
			array(
				'status' => $this->settings['default_task_status'],
				'type' => $this->settings['default_task_type'],
				'priority' => $this->settings['default_task_priority'],
			)
		);
				
		
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($task->getInputFilter());
    		$form->setData($formData);    		
			if ($form->isValid($formData)) 
			{
				$formData['creator'] = $this->identity;
				$task_id = $task->addTask($formData->toArray());
				if($task_id)
				{
					//PM_Model_ActivityLog::logTaskAdd($formData, $id, $formData['project_id'], $this->identity);					
				    if($formData['assigned_to'] != 0)
            		{
            		    //todo
//             			$noti = new PM_Model_Notifications;            			
//             			$noti->sendTaskAssignment($formData);
            		}
            							
					$project = $this->getServiceLocator()->get('PM\Model\Projects');
					$project->updateProjectTaskCount($formData['project_id']);
					$project_data = $project->getProjectById($formData['project_id']);
					$task->updateCompanyId($task_id, $project_data['company_id']);

			    	$this->flashMessenger()->addMessage('Task Added!');
					return $this->redirect()->toRoute('tasks/view', array('task_id' => $task_id));
				}
			} 
			else 
			{
				$view['errors'] = array('Please fix the errors below.');
			}
		 }
		
		$view['form'] = $form;
        $this->layout()->setVariable('layout_style', 'left');
		return $view;
	}
	
	public function removeAction()
	{
		
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$id = $this->params()->fromRoute('task_id');
		$confirm = $this->params()->fromPost('confirm');
		$fail = $this->params()->fromPost('fail');
		
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('pm');
    	}
    	
    	$task_data = $task->getTaskById($id);
    	$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
		}
			    	
    	$view['task'] = $task_data;
    	if(!$view['task'])
    	{
			return $this->redirect()->toRoute('pm');
    	}

    	if($fail)
    	{
			return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
    	}
    	
    	if($confirm)
    	{
    	   	if($task->removeTask($id))
    		{	
				$this->flashMessenger()->addMessage('Task Removed');
				return $this->redirect()->toRoute('projects/view', array('project_id' => $task_data['project_id']));
    		}
    	}
    	
    	$view['file_count'] = $task->getFileCount($id);
    	
		//$this->view->headTitle('Delete Task: '. $this->view->task['name'], 'PREPEND');
		$view['id'] = $id;
		return $this->ajaxOutput($view);
	}	
}