<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/NotesController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Notes Controller
*
* Routes the Notes requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/NotesController.php
*/
class NotesController extends AbstractPmController
{
	/**
	 * Class preDispatch
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
		//$this->layout()->setVariable('layout_style', 'single');
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('sub_menu', 'tasks');
		$this->layout()->setVariable('active_nav', 'bookmarks');
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
		
	    $notes = new PM_Model_Notes;
		$company_id = $this->_getParam("company",FALSE);
    	if($company_id)
    	{
			if(!$this->perm->check($this->identity, 'view_companies'))
	        {
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;
	        }	    		
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data) 
			{
				$this->_helper->redirector('index','companies');
				exit;				
			}
			$this->view->company = $company_data;
			$note_data = $notes->getNotesByCompanyId($company_id);
    	}
    	
		$project_id = $this->_request->getParam('project', FALSE);
		if($project_id) 
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
				$this->_helper->redirector('index','projects');
				exit;				
			}
			
			if(!$project->isUserOnProjectTeam($this->identity, $project_id))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}
						
			$this->view->project = $project_data;
			$note_data = $notes->getNotesByProjectId($project_id);
		}
		
		$task_id = $this->_request->getParam('task', FALSE);
		if($task_id) 
		{
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$task_data = $task->getTaskById($task_id);
			if(!$task_data)
			{
				$this->_helper->redirector('index','tasks');
				exit;				
			}
			
			if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}
						
			$this->view->task = $task_data;
			$note_data = $notes->getNotesByTaskId($task_id);
		}			
    	
    	if(!$company_id && !$project_id && !$task_id)
    	{
    		$view = $this->_getParam("view",FALSE);
    		$note_data = $notes->getAllNotes($view);
    	}
    	
		$view = $this->_getParam("view",FALSE);
		$this->view->active_sub = $view;
		$this->view->notes = $note_data;
	}
	
	/**
	 * Note View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('note_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('notes');
		}
		
		$note = $this->getServiceLocator()->get('PM\Model\Notes');
		$note_data = $note->getNoteById($id);
		$view['note'] = $note_data;
		if (!$note_data) 
		{
			return $this->redirect()->toRoute('notes');
		}
		
		if($note_data['project_id'])
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $note_data['project_id']))
			{
	        	return $this->redirect()->toRoute('pm');				
			}
			
			$view['project'] = $project->getProjectById($note_data['project_id'], array('id', 'name'));
		}
		
		if($note_data['task_id'])
		{
			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
			$view['task'] = $task->getTaskById($note_data['task_id'], array('id', 'name'));
		}		
		
		if($note_data['company_id'] && $note_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	return $this->redirect()->toRoute('pm');			
			}
			
			$company = $this->getServiceLocator()->get('PM\Model\Company');
			$view['company'] = $company->getCompanyById($note_data['company_id'], array('id', 'name'));
		}		

		//$this->view->headTitle('Viewing Note: '. $this->view->note['subject'], 'PREPEND');
		$view['id'] = $id;
		return $this->ajax_output($view);
	}
	
	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id =  $this->params()->fromRoute('note_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('notes');
		}
		
		$note = $this->getServiceLocator()->get('PM\Model\Notes');
		$note_data = $note->getNoteById($id);
		if (!$note_data) 
		{
			return $this->redirect()->toRoute('notes');
		}
		
		if($note_data['project_id'])
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $note_data['project_id']))
			{
	        	return $this->redirect()->toRoute('pm');			
			}			
		}
		
		if($note_data['company_id'] && $note_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	return $this->redirect()->toRoute('pm');				
			}			
		}		
		
		$form = $this->getServiceLocator()->get('PM\Form\NoteForm');
        $view['id'] = $id;
        $form->setData($note_data);
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($note->getInputFilter());
            $form->setData($formData);
            if ($form->isValid($formData)) 
            {
				          	
            	if($note->updateNote($formData->toArray(), $id))
	            {	
	            	$formData['task'] = $note_data['task_id'];
	            	$formData['company'] = $note_data['company_id'];
	            	$formData['project'] = $note_data['project_id'];
	            	//PM_Model_ActivityLog::logNoteUpdate($formData, $id, $this->identity);
	            	
			    	$this->flashMessenger()->addMessage('Note updated!');
					return $this->redirect()->toRoute('notes/view', array('note_id' => $id));
					        		
            	} 
            	else 
            	{
            		$view['errors'] = array('Couldn\'t update note...');
            		$form->setData($formData);
            	}
                
            } 
            else 
            {
            	$view['errors'] = array('Please fix the errors below.');
                $form->setData($formData);
            }
            
	    }
	    
	    $view['form'] = $form;  
		//$this->view->headTitle('Edit Note', 'PREPEND');  
        $this->layout()->setVariable('layout_style', 'right');
        $this->layout()->setVariable('sidebar', 'dashboard');

		return $this->ajax_output($view);
	}
	
	/**
	 * Company Add Page
	 * @return void
	 */
	public function addAction()
	{
		
		$company_id = $this->_request->getParam('company', FALSE);
		if($company_id) 
		{
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				$this->_helper->redirector('index','companies');
				exit;				
			}
			$this->view->company = $company_data;
		}

		$project_id = $this->_request->getParam('project', FALSE);
		if($project_id) 
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
				$this->_helper->redirector('index','projects');
				exit;				
			}
			$this->view->project = $project_data;
		}

		$task_id = $this->_request->getParam('task', FALSE);
		if($task_id) 
		{
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$task_data = $task->getTaskById($task_id);
			if(!$task_data)
			{
				$this->_helper->redirector('index','tasks');
				exit;				
			}
			$this->view->task = $task_data;
		}		
    			
		$note = new PM_Model_Notes;
		$form = $note->getNoteForm(array(
            'action' => '/pm/notes/add',
            'method' => 'post',
        ));
		
		 if ($this->getRequest()->isPost()) 
		 {
    		
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				if((is_numeric($formData['date_hour']) && $formData['date_hour'] <= 24) 
				&& (is_numeric($formData['date_minute']) && $formData['date_minute'] <= 60))
				{	
					$formData['date'] = $formData['date'].' '.$formData['date_hour'].':'.$formData['date_minute'];
				}
				
				if(isset($project_data))
				{
					$formData['company'] = $project_data['company_id'];
				}
				
				if(isset($task_data))
				{
					$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
					$formData['project'] = $task_data['project_id'];
					$temp = $project->getCompanyIdById($task_data['project_id']);
					$formData['company'] = $temp['company_id'];
				}				
				
				if($id = $note->addNote($formData, $this->identity))
				{
					PM_Model_ActivityLog::logNoteAdd($formData, $id, $this->identity);
			    	$this->_flashMessenger->addMessage('Note Added!');
					$this->_helper->redirector('view','notes', 'pm', array('id' => $id));
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
		$this->view->headTitle('Add Note', 'PREPEND');

		$this->view->form = $form;
	}
	
	function removeAction()
	{   		
		$notes = new PM_Model_Notes;
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','notes');
    		exit;
    	}
    	
    	$note_data = $notes->getNoteById($id);
    	$this->view->note = $note_data;
    	if(!$this->view->note)
    	{
			$this->_helper->redirector('index','notes');
			exit;
    	}
    	
    	
		if($note_data['project_id'])
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			if(!$project->isUserOnProjectTeam($this->identity, $note_data['project_id']))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}			
		}
		
		if($note_data['company_id'] && $note_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}			
		}
		
		if($note_data['task_id'] && $note_data['task_id'] == '0') 
		{
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$task_data = $task->getTaskById($note_data['task_id']);
			if(!$task_data)
			{
				$this->_helper->redirector('index','tasks');
				exit;				
			}
			$this->view->task = $task_data;
		}
				
    	if($fail)
    	{
			$this->_helper->redirector('view','notes', 'pm', array('id' => $id));
			exit;   		
    	}
    	
    	if($confirm)
    	{
    	   	if($notes->removeNote($id))
    		{	
				$formData['task'] = $note_data['task_id'];
				$formData['company'] = $note_data['company_id'];
				$formData['project'] = $note_data['project_id'];
				PM_Model_ActivityLog::logNoteRemove($note_data, $id, $this->identity);    			
				$this->_flashMessenger->addMessage('Note Removed');
				if($note_data['task_id'] > 0)
				{
					$this->_helper->redirector('view','tasks', 'pm', array('id' => $note_data['task_id']));
					exit;
				}
				
    			if($note_data['project_id'] > 0)
				{
					$this->_helper->redirector('view','projects', 'pm', array('id' => $note_data['project_id']));
					exit;
				}

    			if($note_data['company_id'] > 0)
				{
					$this->_helper->redirector('view','companies', 'pm', array('id' => $note_data['company_id']));
					exit;
				}				
				
				$this->_helper->redirector('index','notes');
				exit;
				
    		} 
    	}
    	
		$this->view->headTitle('Delete Note: '. $this->view->note['subject'], 'PREPEND');
		$this->view->id = $id;    	
	}	
}