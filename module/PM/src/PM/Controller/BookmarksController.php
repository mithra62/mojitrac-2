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
	    $bookmarks = $this->getServiceLocator()->get('PM\Model\Bookmarks');
	    $id = $this->params()->fromRoute('id');
	    $type = $this->params()->fromRoute('type');
	    $company_id = $project_id = $task_id = false;
	    
		if($type == 'company') 
		{
			$company_id = $id;
			$company = $this->getServiceLocator()->get('PM\Model\Companies');
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				return $this->redirect()->toRoute('companies');
			}
			
			$view['company'] = $company_data;
			$bookmark_data = $bookmarks->getBookmarksByCompanyId($company_id);
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
			$view['project'] = $project_data;
			$bookmark_data = $bookmarks->getBookmarksByProjectId($project_id);
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
			
			$view['task'] = $task_data;
			$bookmark_data = $bookmarks->getBookmarksByTaskId($task_id);
		}			
    	
    	if(!$company_id && !$project_id && !$task_id)
    	{
    		$view = $this->_getParam("view",FALSE);
    		$bookmark_data = $bookmarks->getAllBookmarks($view);
    	}
    	
	    $view['bookmarks'] = $bookmark_data;
	    $view['id'] = $id;
	    $view['type'] = $type;
	    return $view;
	}
	
	/**
	 * Bookmark View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('bookmark_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('pm');
		}
		
		$bookmark = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$bookmark_data = $bookmark->getBookmarkById($id);
		$view['bookmark'] = $bookmark_data;
		if (!$bookmark_data)
		{
			return $this->redirect()->toRoute('pm');
		}
		
		if($bookmark_data['project_id'])
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			if(!$project->isUserOnProjectTeam($this->identity, $bookmark_data['project_id']))
			{
	        	return $this->redirect()->toRoute('pm');
			}			
		}
		
		if($bookmark_data['company_id'] && $bookmark_data['project_id'] == '0')
		{
			if(!$this->perm->check($this->identity, 'view_companies'))
			{
	        	return $this->redirect()->toRoute('pm');				
			}			
		}		

		//$this->view->headTitle('Viewing Bookmark: '. $this->view->bookmark['name'], 'PREPEND');
		$view['id'] = $id;
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Bookmark Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('bookmark_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('bookmarks');
		}

		$bookmark = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$form = $this->getServiceLocator()->get('PM\Form\BookmarkForm');
				
		$bookmark_data = $bookmark->getBookmarkById($id);
		if (!$bookmark_data) 
		{
			return $this->redirect()->toRoute('bookmarks');
		}
        
        $view['id'] = $id;      
        $form->setData($bookmark_data);	
        $view['form'] = $form;
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($bookmark->getInputFilter());
            $form->setData($formData);
            if ($form->isValid($formData)) 
            {
            	if($bookmark->updateBookmark($formData->toArray(), $id))
	            {		            	
			    	$this->flashMessenger()->addMessage('Bookmark updated!');
					return $this->redirect()->toRoute('bookmarks/view', array('bookmark_id' => $id));   
            	} 
            	else 
            	{
            		$view['errors'] = array('Couldn\'t update bookmark...');
            		$form->setData($formData);
            	}
            } 
            else 
            {
            	$view['errors'] = array('Please fix the errors below.');
                $form->setData($formData);
            }
            
	    }	    
		//$this->view->headTitle('Edit Bookmark', 'PREPEND');  
		$view['form_action'] = $this->getRequest()->getRequestUri(); 
		return $view;  	
	}
	
	/**
	 * Bookmark Add Page
	 * @return void
	 */
	public function addAction()
	{
		$id = $this->params()->fromRoute('id');
		$type = $this->params()->fromRoute('type');
		$view = array();
		if($type == 'company') 
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

		if($type == 'project') 
		{
		    $project_id = $id;
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
			    return $this->redirect()->toRoute('projects');
			}
			$view['project'] = $project_data;
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
			
			$view['task'] = $task_data;
		}			

		$bookmark = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$form = $this->getServiceLocator()->get('PM\Form\BookmarkForm');
		if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($bookmark->getInputFilter());
    		$form->setData($formData);
    		    		
			if ($form->isValid($formData)) 
			{
				$formData['owner'] = $this->identity;
				if(isset($project_data))
				{
					$formData['company'] = $project_data['company_id'];
				}
				
				if(isset($task_data))
				{
					$project = $this->getServiceLocator()->get('PM\Model\Projects');
					$formData['project'] = $task_data['project_id'];
					$temp = $project->getCompanyIdById($task_data['project_id']);
					$formData['company'] = $temp['company_id'];
				}	

				$id = $bookmark->addBookmark($formData->toArray());
				if($id)
				{
					$this->flashMessenger()->addMessage('Bookmark Added!');
					return $this->redirect()->toRoute('bookmarks/view', array('bookmark_id' => $id));
				}
				
			} 
			else 
			{
				$view['errors'] = array('Please fix the errors below.');
			}

		}
		
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('layout_style', 'right');
		$view['form'] = $form;
		$view['form_action'] = $this->getRequest()->getRequestUri();
		return $this->ajaxOutput($view);
	}
	
	public function removeAction()
	{   		
		$bookmark = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		
		$id = $this->params()->fromRoute('bookmark_id');
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('pm');
    	}
    	
    	$bookmark_data = $bookmark->getBookmarkById($id);
    	$view['bookmark'] = $bookmark_data;
    	if(!$view['bookmark'])
    	{
			return $this->redirect()->toRoute('pm');
    	}

		$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid($formData))
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('bookmarks/view', array('bookmark_id' => $id));
				}
				
	    	   	if($bookmark->removeBookmark($id))
	    		{	
					$formData['task'] = $bookmark_data['task_id'];
					$formData['company'] = $bookmark_data['company_id'];
					$formData['project'] = $bookmark_data['project_id'];
					$this->flashMessenger()->addMessage('Bookmark Removed');
					if($bookmark_data['task_id'] > 0)
					{
					    return $this->redirect()->toRoute('tasks/view', array('task_id' => $bookmark_data['task_id']));
					}
					
	    			if($bookmark_data['project_id'] > 0)
					{
					    return $this->redirect()->toRoute('projects/view', array('project_id' => $bookmark_data['project_id']));
					}
	
	    			if($bookmark_data['company_id'] > 0)
					{
					    return $this->redirect()->toRoute('companies/view', array('company_id' => $bookmark_data['company_id']));
					}				
					
					return $this->redirect()->toRoute('companies');
					
	    		} 
			}
    	}
    	
		$view['id'] = $id;  
		$view['form'] = $form;
		return $this->ajaxOutput($view); 	
	}		
}