<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/CompaniesController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
* PM - Companies Controller
*
* Routes the company requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/CompaniesController.php
*/
class CompaniesController extends AbstractPmController
{	
	/**
	 * Class preDispatch
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		parent::onDispatch( $e );
		parent::check_permission('view_companies');
		$this->layout()->setVariable('layout_style', 'single');
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('sub_menu', 'companies');
		$this->layout()->setVariable('active_nav', 'companies');
		$this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Companies::types());
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
		//$param = $this->_getParam("view",FALSE);
		$param = $this->params()->fromRoute('company_id');
		$view['active_sub'] = $param;
		$view['company_filter'] = $param;		
		if($this->perm->check($this->identity, 'manage_companies'))
		{
			$company = $this->getServiceLocator()->get('PM\Model\Companies'); 
			$companies = $company->getAllCompanies($view);
			$view['companies'] = $companies;
		}
		else
		{
			$user = $this->getServiceLocator()->get('PM\Model\Users'); 
			$view['companies'] = $user->getAssignedProjectCompanies($this->identity);
		}
		
		return $view;
	}
	
	/**
	 * Company View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('company_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('companies');	
		}
		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$view['company'] = $company->getCompanyById($id);
		if(!$view['company'])
		{
			return $this->redirect()->toRoute('companies');
		}
		
		if($this->perm->check($this->identity, 'view_projects'))
		{
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			$view['projects'] = $project->getProjectsByCompanyId($id, TRUE);
		}
		
		if($this->perm->check($this->identity, 'view_tasks'))
		{		
			$task = $this->getServiceLocator()->get('PM\Model\Tasks');
			$view['tasks'] = $task->getTasksByCompanyId($id);
		}

		if($this->perm->check($this->identity, 'view_files'))
		{
			$file = $this->getServiceLocator()->get('PM\Model\Files');
			$view['files'] = $file->getFilesByCompanyId($id);
		}
		
		if($this->perm->check($this->identity, 'view_company_contacts'))
		{		
			$contacts = $this->getServiceLocator()->get('PM\Model\Contacts');
			$view['contacts'] = $contacts->getContactsByCompanyId($id);
		}
		
		if($this->perm->check($this->identity, 'view_time'))
		{		
			$times = $this->getServiceLocator()->get('PM\Model\Times');
			$not = array('bill_status' => 'paid');
			$view['times'] = $times->getTimesByCompanyId($id, $where, $not);
			$view['hours'] = $times->getTotalTimesByCompanyId($id);
		}

		$bookmarks = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$view['bookmarks'] = $bookmarks->getBookmarksByCompanyId($id);
		
		$notes = $this->getServiceLocator()->get('PM\Model\Notes');
		$view['notes'] = $notes->getNotesByCompanyId($id);
		
		//$this->view->layout_style = 'right';
		
		$view['sub_menu'] = 'company';
		$view['active_sub'] = $this->view->company['type'];		
		//$this->view->headTitle('Viewing Company: '. $this->view->company['name'], 'PREPEND');
		$view['id'] = $view['company_id'] = $id;
		
		return $view;
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