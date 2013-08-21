<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/CompaniesController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Companies Controller
*
* Routes the Companies requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/CompaniesController.php
*/
class Pm_CompaniesController extends PM_Abstract
{

	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        parent::check_permission('view_companies');
        $this->view->headTitle('Companies', 'PREPEND');
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'companies';
        $this->view->active_nav = 'companies';
        $this->view->sub_menu_options = PM_Model_Options_Companies::types();
		$this->view->active_sub = 'None';
		$this->view->title = FALSE;          
	}
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{		
		$view = $this->_getParam("view",FALSE);
		$this->view->active_sub = $view;
		$this->view->company_filter = $view;		
		if($this->perm->check($this->identity, 'manage_companies'))
		{
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$companies = $company->getAllCompanies($view);
			$this->view->companies = $companies;
		}
		else
		{
			$user = new PM_Model_Users(new PM_Model_DbTable_Users);
			$this->view->companies = $user->getAssignedProjectCompanies($this->identity);
		}
	}
	
	/**
	 * Company View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->_helper->redirector('index','companies');
			exit;
		}
		
		$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
		$this->view->company = $company->getCompanyById($id);
		if(!$this->view->company)
		{
			$this->_helper->redirector('index','companies');
			exit;
		}
		
		if($this->perm->check($this->identity, 'view_projects'))
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$this->view->projects = $project->getProjectsByCompanyId($id, TRUE);
		}
		
		if($this->perm->check($this->identity, 'view_tasks'))
		{		
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$this->view->tasks = $task->getTasksByCompanyId($id);
		}

		if($this->perm->check($this->identity, 'view_files'))
		{
			$file = new PM_Model_Files(new PM_Model_DbTable_Files);
			$this->view->files = $file->getFilesByCompanyId($id);
		}
		
		if($this->perm->check($this->identity, 'view_company_contacts'))
		{		
			$contacts = new PM_Model_Contacts;
			$this->view->contacts = $contacts->getContactsByCompanyId($id);
		}
		
		if($this->perm->check($this->identity, 'view_time'))
		{		
			$times = new PM_Model_Times;
			$not = array('bill_status' => 'paid');
			$this->view->times = $times->getTimesByCompanyId($id, $where, $not);
			$this->view->hours = $times->getTotalTimesByCompanyId($id);
		}

		$bookmarks = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
		$this->view->bookmarks = $bookmarks->getBookmarksByCompanyId($id);
		
		$notes = new PM_Model_Notes;
		$this->view->notes = $notes->getNotesByCompanyId($id);
		
		//$this->view->layout_style = 'right';
		
		$this->view->sub_menu = 'company';
		$this->view->active_sub = $this->view->company['type'];		
		$this->view->headTitle('Viewing Company: '. $this->view->company['name'], 'PREPEND');
		$this->view->id = $this->view->company_id = $id;
	}
	
	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		
		if(!$this->perm->check($this->identity, 'manage_companies'))
        {
        	$this->_helper->redirector('index', 'companies', 'pm');
        	exit;
        }
        		
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) {
			$this->_helper->redirector('index','companies');
			exit;
		}
		
		$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
		$form = $company->getCompanyForm(array(
            'action' => '/pm/companies/edit/',
            'method' => 'post',
        ), array('id' => $id));
        
        $this->view->id = $id;
        
        $company_data = $company->getCompanyById($id);
        $form->populate($company_data);	
        	
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
            
            	if($company->updateCompany($formData, $id))
	            {	
			    	$this->_flashMessenger->addMessage('Company updated!');
					$this->_helper->redirector('view','companies', 'pm', array('id' => $id));
					        		
            	} else {
            		$this->view->errors = array('Couldn\'t update company...');
            		$form->populate($formData);
            	}
                
            } else {
            	$this->view->errors = array('Please fix the errors below.');
                $form->populate($formData);
            }
            
	    }
	    
	    $this->view->company_data = $company_data;
	    if($company_data['type'] == '1' || $company_data['type'] == '6')
	    {
	    	Zend_Registry::set('pm_activity_filter', array('company_id' => $id));
	    }
	    
	    $this->view->active_sub = $company_data['type'];	
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		    
		$this->view->headTitle('Edit Company', 'PREPEND');     	
	}
	
	/**
	 * Company Add Page
	 * @return void
	 */
	public function addAction()
	{
		
	    if(!$this->perm->check($this->identity, 'manage_companies'))
        {
        	$this->_helper->redirector('index', 'companies', 'pm');
        	exit;
        }
        		
		$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
		$form = $company->getCompanyForm(array(
            'action' => '/pm/companies/add',
            'method' => 'post',
        ));
		
		 if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				$formData['owner'] = $this->identity;
				if($id = $company->addCompany($formData)){
			    	$this->_flashMessenger->addMessage('Company Added!');
					$this->_helper->redirector('view','companies', 'pm', array('id' => $id));
					exit;
				} 
				else 
				{	
					$this->view->errors = array('Something went wrong...');
				}
			} 
			else 
			{
				$this->view->errors = array('Please fix the errors below.');
			}

		 }
		
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		
		$this->view->headTitle('Add Company', 'PREPEND');

		$this->view->form = $form;
	}
	
	function removeAction()
	{
		
		if(!$this->perm->check($this->identity, 'manage_companies'))
        {
        	$this->_helper->redirector('index', 'companies', 'pm');
        }
        		
		$companies = new PM_Model_Companies(new PM_Model_DbTable_Companies);
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','companies');
    		exit;
    	}
    	
    	$this->view->company = $companies->getCompanyById($id);
    	if(!$this->view->company)
    	{
			$this->_helper->redirector('index','companies');
			exit;
    	}

    	if($fail)
    	{
			$this->_helper->redirector('view','companies', 'pm', array('id' => $id));
			exit;   		
    	}
    	
    	if($confirm)
    	{
    	   	if($companies->removeCompany($id))
    		{	
				$this->_flashMessenger->addMessage('Company Removed');
				$this->_helper->redirector('index','companies');
				exit;
				
    		} 
    	}
    	
    	$this->view->project_count = $companies->getProjectCount($id);
    	$this->view->task_count = $companies->getTaskCount($id);
    	$this->view->file_count = $companies->getFileCount($id);
    	
		$this->view->headTitle('Delete Company: '. $this->view->company['name'], 'PREPEND');
		$this->view->id = $id;    	
	}
	
	public function mapAction()
	{
		if(!$this->perm->check($this->identity, 'view_companies'))
		{
			$this->_helper->redirector('index','index');
			exit;			
		}	
				
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->_helper->redirector('index','companies');
			exit;
		}
		
		$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
		$company_data = $company->getCompanyById($id);
		if(!$company_data)
		{
			$this->_helper->redirector('index','companies');
			exit;
		}
		
		$this->view->company = $company_data;
	}
}