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
class FileRevisionsController extends AbstractPmController
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
					$this->flashMessenger()->addMessage($this->translate('file_updated', 'pm'));
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
		$file_id = $this->params()->fromRoute('file_id');
		if (!$file_id) {
			return $this->redirect()->toRoute('pm');
		}

		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$file_data = $file->getFileById($file_id);
		if(!$file_data)
		{
			return $this->redirect()->toRoute('pm');
		}
    	
		$form = $this->getServiceLocator()->get('PM\Form\File\RevisionForm');		
		$request = $this->getRequest();
		if ($request->isPost()) 
		 {
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($file->revision->getInputFilter(true));
			$formData = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);	
			
			$form->setData($formData);
			if ($form->isValid($formData)) 
			{
				$formData = $form->getData();
				$adapter = $file->getFileTransferAdapter($formData['file_upload']['name']);	
				if ($adapter->isValid())
				{
					if ($adapter->receive($formData['file_upload']['name']))
					{
						$file_info = $adapter->getFileInfo('file_upload');
						
						$formData['creator'] = $this->identity;	
						$formData['owner'] = $this->identity;
						$formData['uploaded_by'] = $this->identity;
						$formData['upload_file_data'] = $file_info['file_upload'];
						$formData['file_data'] = $file_data;
						
						$revision_id = $file->revision->addRevision($file_id, $formData, true);
						if($revision_id)
						{
							if(isset($file_data['project_id']) && $file_data['project_id'] >= 1)
							{
								//$noti = new PM_Model_Notifications;
								//$noti->sendRevisionAdd($id);
							}
	
							//PM_Model_ActivityLog::logFileRevisionAdd($formData, $id, $this->identity);
					    	//$this->_flashMessenger->addMessage('File Revision Added!');
							$this->flashMessenger()->addMessage($this->translate('file_revision_added', 'pm'));
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
		$view['file_data'] = $file_data;
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
	
	public function removeAction()
	{
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->params()->fromRoute('revision_id');
		if (!$id) {
			return $this->redirect()->toRoute('pm');
		}
    	
    	$rev_data = $file->revision->getRevision($id);
    	if(!$rev_data)
    	{
			return $this->redirect()->toRoute('pm');
    	}
    	
    	$file_data = $file->getFileById($rev_data['file_id']);
	    if(!$file_data)
    	{
			return $this->redirect()->toRoute('pm');  		
    	}

    	$total_revisions = $file->revision->getTotalFileRevisions($rev_data['file_id']);
    	$view['total_file_revisions'] = $total_revisions;
    	
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
					return $this->redirect()->toRoute('files/view', array('file_id' => $rev_data['file_id']));
				}
				
	    	   	if($file->revision->removeRevision($id))
	    		{	
					//PM_Model_ActivityLog::logFileRevisionRemove($rev_data, $id, $this->identity);
					  			
					$this->flashMessenger()->addMessage($this->translate('file_revision_removed', 'pm'));
					return $this->redirect()->toRoute('files/view', array('file_id' => $rev_data['file_id']));
					
	    		} 
	    		
			}
    	}
    	
    	$view['form'] = $form;
    	$view['revision_data'] = $rev_data;
    	$view['file_data'] = $file_data;
		$view['id'] = $id;   
		return $this->ajaxOutput($view);
	}
}