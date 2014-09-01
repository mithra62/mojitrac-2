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
		
		$file_revisions = $file->getFileRevisions($id);
		$file_reviews = array();//$file->getFileReviews($id);

		$view['file'] = $file_data;
		$view['revision_history'] = $file_revisions;
		$view['file_reviews'] = $file_reviews;
		$view['id'] = $id;
		return $view;
	}
	
	public function downloadRevisionAction()
	{
		$id = $this->_request->getParam('id', false);
		if (!$id) {
			$this->_helper->redirector('index','index');
		}
		
		$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$rev_data = $file->getRevision($id);
		if (!$rev_data) {
			$this->_helper->redirector('index','index');
		}

		$file_data = $file->getFileById($rev_data['file_id']);
		if (!$file_data) {
			$this->_helper->redirector('index','index');
			exit;
		}
		
		if($file_data['project_id'] != 0)
		{
			//check if the user is on the project's team.
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			if(!$project->isUserOnProjectTeam($this->identity, $file_data['project_id']) && !$this->perm->check($this->identity, 'manage_files'))
			{
				$this->_helper->redirector('index','index');
				exit;
			}		
		}
		
		$download_path = $file->chmkdir($file->getStoragePath(), $file_data['company_id'], $file_data['project_id'], $file_data['task_id']);
		$download_path  = $download_path.DS.$rev_data['stored_name'];
		
		LambLib_Controller_Action_Helper_Utilities::file_download($download_path, $rev_data['file_name']);
		exit;
	}
	
	public function previewRevisionAction()
	{
		$id = $this->_request->getParam('id', false);
		$view_type = $this->_request->getParam('view-type', false);
		$view_size = $this->_request->getParam('view-size', false);
		if (!$id) {
			$this->_helper->redirector('index','index');
			exit;
		}
		
		$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$rev_data = $file->getRevision($id);
		if (!$rev_data) 
		{
			$this->_helper->redirector('index','index');
			exit;
		}

		$file_data = $file->getFileById($rev_data['file_id']);
		if (!$file_data) 
		{
			$this->_helper->redirector('index','index');
			exit;
		}
		
		if($file_data['project_id'] != 0)
		{
			//check if the user is on the project's team.
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			if(!$project->isUserOnProjectTeam($this->identity, $file_data['project_id']) && !$this->perm->check($this->identity, 'manage_files'))
			{
				$this->_helper->redirector('index','index');
				exit;
			}		
		}
		
		$this->view->preview_exists = true;
		$root_path = $file->chmkdir($file->getStoragePath(), $file_data['company_id'], $file_data['project_id'], $file_data['task_id']);
		if(file_exists($root_path.DS.$rev_data['stored_name']))
		{
			//check if we're dealing with an image:
			$image_check = getimagesize($root_path.DS.$rev_data['stored_name']);
			if($image_check)
			{
				$view_size = $file->getPreviewSize($view_size);
				$download_path  = $root_path.DS.$view_size.$rev_data['stored_name'];
				if(!file_exists($download_path))
				{
					if($image_check && is_array($image_check))
					{
						if('image/psd' == $image_check['mime'])
						{
							$psd_path = str_replace('.psd','.jpg',$download_path);
							if(file_exists($psd_path))
							{
								$download_path = $psd_path;
								$rev_data['mime_type'] = 'image/jpeg';
							}
						}
						else
						{
							$file->processImage($rev_data['stored_name'], $root_path, $image_check);
						}
					}
					else
					{
						$this->view->preview_exists = false;
					}
				}
			}
			else
			{
				$this->view->preview_exists = false;
			}
		}
		else
		{
			$this->view->preview_exists = false;
		}
		
		if($view_type == 'html')
		{
			header('Content-type: '.$rev_data['mime_type']);
			header("Content-Length: " . filesize($download_path));		
			
			$fp = fopen($download_path, 'rb');
			fpassthru($fp);
			exit;
		}
		
		$this->view->rev_data = $rev_data;
		$this->view->file_reviews = $file->getReviewsByRevisionId($id);
	}

	public function viewReviewAction()
	{
		$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$id = $this->_request->getParam('id', false);
		
	    if(!$id)
    	{
    		$this->_helper->redirector('index','index');
    		exit;
    	}
    	
    	$review_data = $file->getReviewById($id);
    	if(!$review_data)
    	{
			$this->_helper->redirector('index','index');
			exit;
    	}
    	
    	$revision_data = $file->getRevision($review_data['revision_id']);
    	$file_data = $file->getFileById($review_data['file_id']);
    	
    	$this->view->file_data = $file_data;
    	$this->view->review_data = $review_data;
    	$this->view->revision_data = $revision_data;
	}
	
	/**
	 * File Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->_request->getParam('id', false);
	    if(!$id)
        {
			$this->_helper->redirector('index','index');
			exit;        	
        }
        
        $file = new PM_Model_Files(new PM_Model_DbTable_Files);
	    $file_data = $file->getFileById($id);
        if(!$file_data)
        {
			$this->_helper->redirector('index','index');
			exit;        	
        }
        
		$form = $file->getFileForm(array(
            'action' => '/pm/files/edit',
            'method' => 'post',
        ), true);
        
        $form->populate($file_data);
		
		if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{	
				$formData['creator'] = $this->identity;
			    if($file->updateFile($formData, $id))
	            {
	            	$formData['company'] = $file_data['company_id'];
	            	$formData['project'] = $file_data['project_id'];
	            	$formData['task'] = $file_data['task_id'];
	            	PM_Model_ActivityLog::logFileUpdate($formData, $id, $this->identity);
			    	$this->_flashMessenger->addMessage('File updated!');
					$this->_helper->redirector('view', 'files', 'pm', array('id' => $id));    
					        		
            	} 
            	else 
            	{
            		$this->view->errors = array('Couldn\'t update file...');
            		$form->populate($formData);
            	}
			}
		}
		
		$this->view->id = $id;
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		
		$this->view->headTitle('Edit File', 'PREPEND');

		$this->view->file_data = $file_data;
		$this->view->form = $form;		
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
							//PM_Model_ActivityLog::logFileAdd($formData, $id, $this->identity);

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
					
					$this->_flashMessenger->addMessage('File Removed');
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
	
	public function addRevisionAction()
	{
		$file_id = $this->_getParam("file",false);
	    if(!$file_id)
    	{
			$this->_helper->redirector('index','index');
			exit;
    	}		
		
    	$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$file_data = $file->getFileById($file_id);		
		if(!$file_data)
    	{
			$this->_helper->redirector('index','index');
			exit;
    	}
    	
		$form = $file->getFileRevisionForm(array(
            'action' => '/pm/files/add-revision/file/'.$file_id,
            'method' => 'post',
        ));
		
		 if ($this->getRequest()->isPost()) 
		 {
    		
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData) && $form->file->isUploaded()) 
			{
				if($form->file->receive()) //move the file to storage
				{
		
					$file_info = $form->file->getFileInfo();
					$formData['creator'] = $this->identity;	
					$formData['owner'] = $this->identity;
					$formData['uploaded_by'] = $this->identity;
					
					$file_info = $file_info['file'];		
					$path = $file->chmkdir($file_info['destination'], 
								   $file_data['company_id'], 
								   $file_data['project_id'], 
								   $file_data['task_id']
					);		
					
					$formData['extension'] = $file->get_file_extension($file_info['tmp_name']);
					$formData['size'] = filesize($file_info['tmp_name']);
					$formData['name'] = $file_info['name'];
					$formData['type'] = $file_info['type'];
					$formData['stored_name'] = mktime().'.'.$formData['extension'];
					$new_name = $path.DS.$formData['stored_name'];
					if(!rename($file_info['tmp_name'],$new_name))
					{
						return false;
					}	

					$formData['stored_path'] = $path;	
					$formData['file_id'] = $file_id;
					$formData['task'] = $file_data['task_id'];
					$formData['company'] = $file_data['company_id'];
					$formData['project'] = $file_data['project_id'];					
					
					if($id = $file->addRevision($file_id, $formData))
					{
						if(isset($file_data['project_id']) && $file_data['project_id'] >= 1)
						{
							$noti = new PM_Model_Notifications;
							$noti->sendRevisionAdd($id);
						}

						PM_Model_ActivityLog::logFileRevisionAdd($formData, $id, $this->identity);
				    	$this->_flashMessenger->addMessage('File Revision Added!');
						$this->_helper->redirector('view','files', 'pm', array('id' => $file_id));					
						exit;
					}
					else
					{
						$this->view->errors = array('Couldn\'t upload file :(');
					}
								
				} 
				else 
				{
					$this->view->errors = array('Couldn\'t move file :(');
				}
				
			} 
			else 
			{
				$this->view->errors = array('Please fix the errors below.');
			}

		}
				
		$this->view->file = $file_data;
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		
		$this->view->headTitle('Add File Revision', 'PREPEND');
		$this->view->form = $form;		
	}
	
	public function addReviewAction()
	{
		
		$file_id = $this->_getParam("file",false);
	    if(!$file_id)
    	{
			$this->_helper->redirector('index','index');
			exit;
    	}		
		
    	$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$file_data = $file->getFileById($file_id);		
		if(!$file_data)
    	{
			$this->_helper->redirector('index','index');
			exit;
    	}
    	
    	$file_revisions = $file->getFileRevisions($file_id);
    	
		$form = $file->getFileReviewForm(array(
            'action' => '/pm/files/add-review/file/'.$file_id,
            'method' => 'post',
        ));
        
        if ($this->getRequest()->isPost()) 
		{	
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				$formData['file_id'] = $file_id;
				$formData['reviewer_id'] = $this->identity;
				if($id = $file->addReview($formData))
				{
	    			$file_data = $file->getFileById($formData['file_id']);
					$formData['task'] = $file_data['task_id'];
					$formData['company'] = $file_data['company_id'];
					$formData['project'] = $file_data['project_id'];
					$formData['revision_id'] = $formData['review_revision'];
					PM_Model_ActivityLog::logFileReviewAdd($formData, $id, $this->identity);
					
					$this->_flashMessenger->addMessage('Review Added');
					$this->_helper->redirector('view','files', 'pm', array('id' => $file_id));
					exit;
				}
			}
		}        
        
        $this->view->file = $file_data;
        $this->view->file_revisions = $file_revisions;
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		
		$this->view->headTitle('Add File Review', 'PREPEND');
		$this->view->form = $form;			
	}
	
	public function removeRevisionAction()
	{   		
		$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$id = $this->_request->getParam('id', false);
		$confirm = $this->_getParam("confirm",false);
		$fail = $this->_getParam("fail",false);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','index');
    		exit;
    	}
    	
    	$rev_data = $file->getRevision($id);
    	if(!$rev_data)
    	{
			$this->_helper->redirector('index','index');
			exit;
    	}
    	
    	$file_data = $file->getFileById($rev_data['file_id']);
	    if(!$file_data)
    	{
			$this->_helper->redirector('index','index');
			exit;   		
    	}

    	$total_revisions = $file->getTotalFileRevisions($rev_data['file_id']);
    	$this->view->total_file_revisions = $total_revisions;
    	
    	if($fail)
    	{
			$this->_helper->redirector('view','files', 'pm', array('id' => $rev_data['file_id']));
			exit;   		
    	}
				    	
    	if($confirm)
    	{
    	   	if($file->removeRevision($id))
    		{	
				$formData['task'] = $file_data['task_id'];
				$formData['company'] = $file_data['company_id'];
				$formData['project'] = $file_data['project_id'];
				$formData['file_id'] = $rev_data['file_id'];
				PM_Model_ActivityLog::logFileRevisionRemove($rev_data, $id, $this->identity);
				  			
				$this->_flashMessenger->addMessage('Revision Removed');
				$this->_helper->redirector('view','files', 'pm', array('id' => $rev_data['file_id']));
				exit;
				
    		} 
    	}
    	    	
    	$this->view->file = $rev_data;
    	$this->view->file_data = $file_data;
		$this->view->headTitle('Delete File Revision: '. $rev_data['file_name'], 'PREPEND');
		$this->view->id = $id;    	
	}
	
	public function removeReviewAction()
	{
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->_request->getParam('id', false);
		$confirm = $this->_getParam("confirm",false);
		$fail = $this->_getParam("fail",false);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','index');
    		exit;
    	}
    	
    	$review_data = $file->getReviewById($id);
    	if(!$review_data)
    	{
			$this->_helper->redirector('index','index');
			exit;
    	}
    	
    	if ($this->getRequest()->isPost()) 
		{    	
	    	if($fail)
	    	{
				$this->_helper->redirector('view','files', 'pm', array('id' => $review_data['file_id']));
				exit;   		
	    	}
				    	
	    	if($confirm)
	    	{   		
	    	   	if($file->removeReview($id))
	    		{	
					
	    			$file_data = $file->getFileById($review_data['file_id']);
					$review_data['task'] = $file_data['task_id'];
					$review_data['company'] = $file_data['company_id'];
					$review_data['project'] = $file_data['project_id'];
					PM_Model_ActivityLog::logFileReviewRemove($review_data, $id, $this->identity);
	    			
					$this->_flashMessenger->addMessage('Review Removed');
					$this->_helper->redirector('view','files', 'pm', array('id' => $review_data['file_id']));
					exit;
					
	    		} 
	    	}
		}
    	    	
    	$this->view->file = $review_data;
		$this->view->headTitle('Delete File Review: '. $review_data['file_name'], 'PREPEND');
		$this->view->id = $id;    	
	}	
}