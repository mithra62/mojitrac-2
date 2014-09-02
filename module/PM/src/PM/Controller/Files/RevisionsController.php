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

namespace PM\Controller\Files;

use PM\Controller\AbstractPmController;

/**
 * PM - File Revisions Controller
 *
 * Routes the File Revisions requests
 *
 * @package 	Files\Revisions
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/FilesController.php
 */
class RevisionsController extends AbstractPmController
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
	
	public function downloadAction()
	{
		$id = $this->params()->fromRoute('revision_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}

		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$rev_data = $file->revision->getRevision($id);
		
		if (!$rev_data) {
			return $this->redirect()->toRoute('pm');
		}

		$file_data = $file->getFileById($rev_data['file_id']);
		if (!$file_data) {
			return $this->redirect()->toRoute('pm');
		}
		
		if($file_data['project_id'] != 0)
		{
			//check if the user is on the project's team.
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $file_data['project_id']) && !$this->perm->check($this->identity, 'manage_files'))
			{
				return $this->redirect()->toRoute('pm');
			}		
		}
		
		$download_path = $file->checkMakeDirectory($file->getStoragePath(), $file_data['company_id'], $file_data['project_id'], $file_data['task_id']);
		$download_path  = $download_path.DS.$rev_data['stored_name'];
		if(file_exists($download_path) && is_readable($download_path))
		{
			return $this->downloadFile($download_path, $rev_data['file_name']);
		}
		
		$this->flashMessenger()->addErrorMessage($this->translate('file_not_found', 'pm'));
		return $this->redirect()->toRoute('files/view', array('file_id' => $rev_data['file_id']));  	  
	}
	
	public function previewAction()
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
	
	public function addAction()
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
	
	public function removeAction()
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
}