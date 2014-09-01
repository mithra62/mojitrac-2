<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/FilesController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Files Controller
 *
 * Routes the Files requests
 *
 * @package 	Files
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/FilesController.php
 */
class FilesController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        parent::check_permission('view_files');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('active_nav', 'projects');
        $this->layout()->setVariable('sub_menu', 'files');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'None');
		return $e;
	}
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
	public function indexAction()
	{
		$company_id = $this->_request->getParam('company', false);
		$project_id = $this->_request->getParam('project', false);
		$task_id = $this->_request->getParam('task', false);
		$files = new PM_Model_Files(new PM_Model_DbTable_Files);
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
				$company_id = $company_data = false;
				$this->view->files = $files->getAllFiles($view);
			}			
			
       		$this->view->sub_menu = 'company';
       		$this->view->active_sub = 'projects';
        	$this->view->active_nav = 'companies';			
			$this->view->files = $files->getFilesByCompanyId($company_id);
			$this->view->company_data = $company_data;
		}
		elseif($project_id)
		{
			if(!$this->perm->check($this->identity, 'view_projects'))
	        {
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;
	        }
	        
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
				$company_id = $company_data = false;
				$this->view->files = $files->getAllFiles($view);
			}
			
			if(!$project->isUserOnProjectTeam($this->identity, $project_id))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}
			
       		$this->view->sub_menu = 'company';
       		$this->view->active_sub = 'projects';
        	$this->view->active_nav = 'projects';			
			$this->view->files = $files->getFilesByProjectId($project_id);
			$this->view->project_data = $project_data;			
		}
		elseif($task_id)
		{
			if(!$this->perm->check($this->identity, 'view_projects'))
	        {
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;
	        }
	        
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$task_data = $task->getTaskById($task_id);
			if(!$task_data)
			{
				$company_id = $company_data = false;
				$this->view->files = $files->getAllFiles($view);
			}
			
			if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}			
			
       		$this->view->sub_menu = 'company';
       		$this->view->active_sub = 'projects';
        	$this->view->active_nav = 'projects';			
			$this->view->files = $files->getFilesByProjectId($project_id);
			$this->view->task_data = $task_data;			
		}		
		else
		{	
			$view = $this->_getParam("view",false);
			$this->view->active_sub = $view;
		    $this->view->files = $files->getAllFiles($view);
		}
	}
	
	/**
	 * File View Action
	 * @return \Zend\Http\Response
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('file_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}

		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$file_data = $file->getFileById($id);
		if(!$file_data)
		{
			return $this->redirect()->toRoute('pm');
		}
		
		if($file_data['project_id'])
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $file_data['project_id']) && !$this->perm->check($this->identity, 'manage_files'))
			{
	        	return $this->redirect()->toRoute('projects/view', array('project_id' => $file_data['project_id']));				
			}			
		}
		
		if($file_data['company_id'] && $file_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	return $this->redirect()->toRoute('projects/view', array('project_id' => $file_data['project_id']));				
			}			
		}		
		
		$file_revisions = $file->revision->getFileRevisions($id);
		$file_reviews = array();//$file->getFileReviews($id);

		$view['file'] = $file_data;
		$view['revision_history'] = $file_revisions;
		$view['file_reviews'] = $file_reviews;
		$view['id'] = $id;
		return $view;
	}
	
	/**
	 * File Edit Page
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('file_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}

		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$file_data = $file->getFileById($id);
		if(!$file_data)
		{
			return $this->redirect()->toRoute('pm');
		}

		$form = $this->getServiceLocator()->get('PM\Form\FileForm');
        $form->setData($file_data);
		$request = $this->getRequest();
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		
		if ($this->getRequest()->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($file->getInputFilter());
			$form->setData($formData);
			if ($form->isValid()) 
			{	
				$formData['creator'] = $this->identity;
			    if($file->updateFile($formData, $id))
	            {
					$this->flashMessenger()->addMessage($translate('file_updated', 'pm'));
					return $this->redirect()->toRoute('files/view', array('file_id' => $id));  	        		
            	} 
            	else 
            	{
            		$this->view->errors = array('Couldn\'t update file...');
            	}
			}
		}
		
		$view = array();
		$view['id'] = $id;
		$view['file_data'] = $file_data;
		$view['form'] = $form;
		
		$this->layout()->setVariable('layout_style', 'left');
		$view['form_action'] = $this->getRequest()->getRequestUri();
		return $this->ajaxOutput($view);
	}
	
	/**
	 * File Add Page
	 * @return void
	 */
	public function addAction()
	{
		$id = $this->params()->fromRoute('id');
		$type = $this->params()->fromRoute('type');
		$view = array();
		$view['file_errors'] = false;
		if($type == 'company') 
		{
			$company_id = $id;
			$company = $this->getServiceLocator()->get('PM\Model\Companies');
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				return $this->redirect()->toRoute('companies');
			}
			
			$view['company_data'] = $company_data;
		}

		if($type == 'project') 
		{
		    $project_id = $id;
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
			    return $this->redirect()->toRoute('projects');
			}
			$view['project_data'] = $project_data;
		}

		if($type == 'task') 
		{
		    $task_id = $id;
			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
			$task_data = $task->getTaskById($task_id);
			if(!$task_data)
			{
				return $this->residrect()->toRoute('tasks');
			}
			
			$view['task_data'] = $task_data;
		}	
				
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$form = $this->getServiceLocator()->get('PM\Form\FileForm');
		$request = $this->getRequest();
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		if ($request->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($file->getInputFilter(true));
			$formData = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);	
			$form->setData($formData);
			if ($form->isValid()) 
			{
				$formData = $form->getData();
				$adapter = $file->getFileTransferAdapter($formData['file_upload']['name']);			
				if ($adapter->isValid())
				{
					if ($adapter->receive($formData['file_upload']['name']))
					{
						if(isset($project_data))
						{
							$formData['company_id'] = $project_data['company_id'];
						}
						
						if(isset($task_data))
						{
							$project = $this->getServiceLocator()->get('PM\Model\Projects');
							$formData['project_id'] = $task_data['project_id'];
							$formData['task_id'] = $task_data['id'];
							$temp = $project->getCompanyIdById($task_data['project_id']);
							$formData['company_id'] = $temp['company_id'];
						}
										
						$file_info = $adapter->getFileInfo('file_upload');
						$formData['creator'] = $this->identity;	
						$formData['owner'] = $this->identity;
						$formData['uploaded_by'] = $this->identity;					
						$file_id = $file->addFile($formData, $file_info['file_upload']);
						if($file_id)
						{
							$this->flashMessenger()->addMessage($translate('file_added', 'pm'));
							return $this->redirect()->toRoute('files/view', array('file_id' => $file_id));
						}
						else
						{
							$view['file_errors'] = array('Couldn\'t upload file :(');
						}
					}		
				} 
				else 
				{
					$view['file_errors'] = $adapter->getMessages();
				}
			} 
			else 
			{
				$view['errors'] = array('Please fix the errors below.');
			}
		}

		$this->layout()->setVariable('layout_style', 'left');
		$form->addFileField();
		$view['form_action'] = $this->getRequest()->getRequestUri();
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Removes a file
	 */
	public function removeAction()
	{   
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');

		$id = $this->params()->fromRoute('file_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}
    	
    	$file_data = $file->getFileById($id);
    	if(!$file_data)
    	{
			return $this->redirect()->toRoute('pm');
    	}

    	$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid())
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('files/view', array('file_id' => $id));
				}
				
	    	   	if($file->removeFile($id))
	    		{	
					$formData['task'] = $file_data['task_id'];
					$formData['company'] = $file_data['company_id'];
					$formData['project'] = $file_data['project_id'];
					
					$this->flashMessenger()->addMessage($translate('file_removed', 'pm'));
					if($file_data['task_id'] > 0)
					{
						return $this->redirect()->toRoute('tasks/view', array('task_id' => $file_data['task_id']));
					}
					
	    			if($file_data['project_id'] > 0)
					{
						return $this->redirect()->toRoute('projects/view', array('project_id' => $file_data['project_id']));
					}
	
	    			if($file_data['company_id'] > 0)
					{
						return $this->redirect()->toRoute('companies/view', array('company_id' => $file_data['company_id']));
					}
					
					return $this->redirect()->toRoute('pm');
	    		} 
	    		else
	    		{
	    			$view['errors'] = array('Couldn\'t remove the file :(');
	    		}
    		}
    	
		}

		$view['file_data'] = $file_data;
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);		
	}
}