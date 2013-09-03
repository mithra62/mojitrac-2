<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/BookmarksController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Bookmarks Controller
*
* Routes the bookmark requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/BookmarksController.php
*/
class BookmarksController extends AbstractPmController
{
	
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        $this->view->headTitle('Bookmarks', 'PREPEND');
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'tasks';
        $this->view->active_nav = 'bookmarks';
		$this->view->active_sub = 'None';
		$this->view->sub_menu_options = PM_Model_Options_Projects::status();
		$this->view->title = FALSE;          
	}
	
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
	    $bookmarks = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
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
			$bookmark_data = $bookmarks->getBookmarksByCompanyId($company_id);
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
			$bookmark_data = $bookmarks->getBookmarksByProjectId($project_id);
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
			$bookmark_data = $bookmarks->getBookmarksByTaskId($task_id);
		}			
    	
    	if(!$company_id && !$project_id && !$task_id)
    	{
    		$view = $this->_getParam("view",FALSE);
    		$bookmark_data = $bookmarks->getAllBookmarks($view);
    	}
    	
		$view = $this->_getParam("view",FALSE);
		$this->view->active_sub = $view;
	    $this->view->bookmarks = $bookmark_data;
	}
	
	/**
	 * Company View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) {
			$this->_helper->redirector('index','bookmarks');
		}
		
		$bookmark = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
		$bookmark_data = $bookmark->getBookmarkById($id);
		$this->view->bookmark = $bookmark_data;
		if (!$bookmark_data) {
			$this->_helper->redirector('index','index');
		}
		
		if($bookmark_data['project_id'])
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			if(!$project->isUserOnProjectTeam($this->identity, $bookmark_data['project_id']))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}			
		}
		
		if($bookmark_data['company_id'] && $bookmark_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}			
		}		

		$this->view->headTitle('Viewing Bookmark: '. $this->view->bookmark['name'], 'PREPEND');
		$this->view->id = $id;
	}
	
	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) {
			$this->_helper->redirector('index','bookmarks');
		}
		
		$bookmark = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
		$bookmark_data = $bookmark->getBookmarkById($id);
		if (!$bookmark_data) {
			$this->_helper->redirector('index','bookmarks');
		}
			
		$form = $bookmark->getBookmarkForm(array(
            'action' => '/pm/bookmarks/edit/',
            'method' => 'post',
        ), array('id' => $id));
        
        $this->view->id = $id;
        
      
        $form->populate($bookmark_data);	
        
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) 
            {
				          	
            	if($bookmark->updateBookmark($formData, $id))
	            {	
					$formData['task'] = $bookmark_data['task_id'];
					$formData['company'] = $bookmark_data['company_id'];
					$formData['project'] = $bookmark_data['project_id'];  	            	
	            	PM_Model_ActivityLog::logBookmarkUpdate($formData, $id, $this->identity);
			    	$this->_flashMessenger->addMessage('Bookmark updated!');
					$this->_helper->redirector('view','bookmarks', 'pm', array('id' => $id));    
					        		
            	} 
            	else 
            	{
            		$this->view->errors = array('Couldn\'t update bookmark...');
            		$form->populate($formData);
            	}
                
            } 
            else 
            {
            	$this->view->errors = array('Please fix the errors below.');
                $form->populate($formData);
            }
            
	    }
	    
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		    
		$this->view->headTitle('Edit Bookmark', 'PREPEND');     	
	}
	
	/**
	 * Company Add Page
	 * @return void
	 */
	public function addAction()
	{
		$id = $this->params()->fromRoute('id');
		$type = $this->params()->fromRoute('type');
		$view = array();
		if($type == 'companies') 
		{
			$company_id = $id;
			$company = $this->getServiceLocator()->get('PM\Model\Companies');
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				return $this->redirect()->toRoute('companies');				
			}
			
			$view['company'] = $company_data;
		}

		if($type == 'projects') 
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

		if($type == 'tasks') 
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

		$bookmark = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$form = $this->getServiceLocator()->get('PM\Form\BookmarkForm');
        		
		 if ($this->getRequest()->isPost()) 
		 {
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				
				$formData['owner'] = $this->identity;
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
							
				if($id = $bookmark->addBookmark($formData))
				{
					PM_Model_ActivityLog::logBookmarkAdd($formData, $id, $this->identity); 
			    	$this->_flashMessenger->addMessage('Bookmark Added!');
					$this->_helper->redirector('view','bookmarks', 'pm', array('id' => $id));
										
					exit;
				}
				
			} 
			else 
			{
				$this->view->errors = array('Please fix the errors below.');
			}

		}
		
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('layout_style', 'right');
		//$this->view->headTitle('Add Bookmark', 'PREPEND');
		$view['form'] = $form;		
		return $this->ajax_output($view);
	}
	
	function removeAction()
	{   		
		$bookmark = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','bookmarks');
    		exit;
    	}
    	
    	$bookmark_data = $bookmark->getBookmarkById($id);
    	$this->view->bookmark = $bookmark_data;
    	if(!$this->view->bookmark)
    	{
			$this->_helper->redirector('index','bookmarks');
			exit;
    	}

    	if($fail)
    	{
			$this->_helper->redirector('view','bookmarks', 'pm', array('id' => $id));
			exit;   		
    	}
    	
    	if($confirm)
    	{
    	   	if($bookmark->removeBookmark($id))
    		{	
				$formData['task'] = $bookmark_data['task_id'];
				$formData['company'] = $bookmark_data['company_id'];
				$formData['project'] = $bookmark_data['project_id'];    			
    			PM_Model_ActivityLog::logBookmarkRemove($bookmark_data, $id, $this->identity); 
				$this->_flashMessenger->addMessage('Bookmark Removed');
				if($bookmark_data['task_id'] > 0)
				{
					$this->_helper->redirector('view','tasks', 'pm', array('id' => $bookmark_data['task_id']));
					exit;
				}
				
    			if($bookmark_data['project_id'] > 0)
				{
					$this->_helper->redirector('view','projects', 'pm', array('id' => $bookmark_data['project_id']));
					exit;
				}

    			if($bookmark_data['company_id'] > 0)
				{
					$this->_helper->redirector('view','companies', 'pm', array('id' => $bookmark_data['company_id']));
					exit;
				}				
				
				$this->_helper->redirector('index','bookmarks');
				exit;
				
    		} 
    	}
    	
		$this->view->headTitle('Delete Bookmark: '. $this->view->note['subject'], 'PREPEND');
		$this->view->id = $id;    	
	}		
}