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
		//$this->layout()->setVariable('layout_style', 'single');
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
			$view['projects'] = $project->getProjectsByCompanyId($id, FALSE);
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
			$view['times'] = $times->getTimesByCompanyId($id, null, $not);
			$view['hours'] = $times->getTotalTimesByCompanyId($id);
		}

		$bookmarks = $this->getServiceLocator()->get('PM\Model\Bookmarks');
		$view['bookmarks'] = $bookmarks->getBookmarksByCompanyId($id);
		
		$notes = $this->getServiceLocator()->get('PM\Model\Notes');
		$view['notes'] = $notes->getNotesByCompanyId($id);
		
		//$this->view->layout_style = 'right';
		
		
		$view['sub_menu'] = 'company';
		$view['layout_style'] = 'single';
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
        	return $this->redirect()->toRoute('companies');
        }
        		
		$id = $this->params()->fromRoute('company_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$form = $this->getServiceLocator()->get('PM\Form\CompanyForm');
        
		$view = array();
        $view['id'] = $id;
        
        $company_data = $company->getCompanyById($id);
        $request = $this->getRequest();
        $form->setData($company_data);        
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($company->getInputFilter());  
            $form->setData($request->getPost());
            
            if ($form->isValid($formData)) 
            {        
            	if($company->updateCompany($formData, $id))
	            {	
			    	$this->flashMessenger()->addMessage('Company updated!');
			    	return $this->redirect()->toRoute('companies/view', array('company_id' => $id));
					        		
            	} else {
            		$view['errors'] = array('Couldn\'t update company...');
            		$form->setData($formData);
            	}
                
            } else {
            	$view['errors'] = array('Please fix the errors below.');
                $form->setData($formData);
            }
            
	    }
	    
	    $view['company_data'] = $company_data;
	    if($company_data['type'] == '1' || $company_data['type'] == '6')
	    {
	    	//Zend_Registry::set('pm_activity_filter', array('company_id' => $id));
	    }
	    
	    $view['form'] = $form;
	    $view['active_sub'] = $company_data['type'];	
        //$view['layout_style'] = 'right';
        $view['sidebar'] = 'dashboard';		
		$this->layout()->setVariable('layout_style', 'left');    
		//$this->view->headTitle('Edit Company', 'PREPEND');  
		return $view;   	
	}
	
	/**
	 * Company Add Page
	 * @return void
	 */
	public function addAction()
	{
		
	    if(!$this->perm->check($this->identity, 'manage_companies'))
        {
        	return $this->redirect()->toRoute('companies');
        }
        		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$form = $this->getServiceLocator()->get('PM\Form\CompanyForm');
		$request = $this->getRequest();
		if ($request->isPost()) 
		{
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($company->getInputFilter());  
            $form->setData($request->getPost());
            
			if ($form->isValid($formData)) 
			{
				
				$formData['owner'] = $this->identity;
				$company_id = $company->addCompany($formData);
				if($company_id)
				{
			    	$this->flashMessenger()->addMessage('Company Added!');
					return $this->redirect()->toRoute('companies/view', array('company_id' => $company_id));
				} 
				else 
				{	
					$view['errors'] = array('Something went wrong...');
				}
			} 
			else 
			{
				$view['errors'] = array('Please fix the errors below.');
			}

		 }
		
        $view['layout_style'] = 'right';
        $view['sidebar'] = 'dashboard';		
		//$this->view->headTitle('Add Company', 'PREPEND');

		$view['form'] = $form;
		$this->layout()->setVariable('layout_style', 'left');
		return $view;
	}
	
	function removeAction()
	{
		
		if(!$this->perm->check($this->identity, 'manage_companies'))
        {
        	return $this->redirect()->toRoute('companies');
        }
        
		$companies = $this->getServiceLocator()->get('PM\Model\Companies');
		$id = $this->params()->fromRoute('company_id');
		$confirm = $this->params()->fromPost('confirm');
		$fail = $this->params()->fromPost('fail');
		
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('companies');
    	}
    	
    	$view = array();
    	$view['company'] = $companies->getCompanyById($id);
    	if(!$view['company'])
    	{
			return $this->redirect()->toRoute('companies');
    	}

    	if($fail)
    	{
    		return $this->redirect()->toRoute('companies/view',  array('company_id' => $id));
    	}
    	
    	if($confirm)
    	{
    	   	if($companies->removeCompany($id))
    		{	
				$this->flashMessenger()->addMessage('Company Removed');
				return $this->redirect()->toRoute('companies');
    		} 
    	}
    	
    	$view['project_count'] = $companies->getProjectCount($id);
    	$view['task_count'] = $companies->getTaskCount($id);
    	$view['file_count'] = $companies->getFileCount($id);
    	
		//$this->view->headTitle('Delete Company: '. $this->view->company['name'], 'PREPEND');
		$view['id'] = $id;

		return $view;
	}
	
	public function mapAction()
	{
		if(!$this->perm->check($this->identity, 'view_companies'))
		{
			return $this->redirect()->toRoute('pm');			
		}	
				
		$id = $this->params()->fromRoute('company_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$company_data = $company->getCompanyById($id);
		if(!$company_data)
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$view['company'] = $company_data;
		return $view;
	}
}