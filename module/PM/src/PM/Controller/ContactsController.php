<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/ContactsController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Contacts Controller
*
* Routes the Contacts requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/ContactsController.php
*/
class Pm_ContactsController extends PM_Abstract
{
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        parent::check_permission('view_company_contacts');
        $this->view->headTitle('Company Contacts', 'PREPEND'); 
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'companies';
        $this->view->active_nav = 'companies';
        $this->view->sub_menu_options = PM_Model_Options_Companies::types();
        $this->view->uri = $this->_request->getPathInfo();
		$this->view->active_sub = 'None';
		$this->view->title = FALSE;          
	}
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{
		
	    $contacts = new PM_Model_Contacts;
		$view = $this->_getParam("view",FALSE);
		$this->view->company = FALSE;
		$company_id = $this->_getParam("company",FALSE);
		if($company_id)
		{
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$this->view->company = $company->getCompanyById($company_id);
			if(!$this->view->company)
			{
				$company_id = FALSE;
			}
		}
		
		if($company_id)
		{
			$this->view->contacts = $contacts->getContactsByCompanyId($company_id);
		}
		else
		{
			$this->view->contacts = $contacts->getAllContacts($view);
		}
		$this->view->active_sub = $view;
		$this->view->title = FALSE;
	}
	
	/**
	 * Company View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) {
			$this->_helper->redirector('index','companies');
			exit;
		}
		
		$contact = new PM_Model_Contacts;
		$this->view->contact = $contact->getContactById($id);
		if(!$this->view->contact)
		{
			$this->_helper->redirector('index','contacts');
			exit;
		}
			
		$this->view->title = FALSE;
		$this->view->headTitle('Viewing Contact: '. $this->view->contact['first_name'].' '.$this->view->contact['last_name'], 'PREPEND');
		$this->view->id = $id;
	}
	
	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		
	    if(!$this->perm->check($this->identity, 'manage_company_contacts'))
        {
        	$this->_helper->redirector('index', 'contacts', 'pm');
        	exit;
        }
        		
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) {
			$this->_helper->redirector('index','contacts');
			exit;
		}
		
		$contact = new PM_Model_Contacts;
		$contact_data = $contact->getContactById($id);
		if(!$contact_data)
		{
			$this->_helper->redirector('index','contacts');
			exit;
		}

		$form = $contact->getContactForm(array(
            'action' => '/pm/contacts/edit/',
            'method' => 'post',
        ), array('id' => $id));
        
        $this->view->id = $id;
        
        $form->populate($contact->getContactById($id));	
        	
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) 
            {
            
            	if($contact->updateContact($formData, $id))
	            {	
			    	$this->_flashMessenger->addMessage('Contact updated!');
					$this->_helper->redirector('view','contacts', 'pm', array('id' => $id));
					        		
            	} 
            	else 
            	{
            		$this->view->errors = array('Couldn\'t update contact...');
            		$form->populate($formData);
            	}
                
            } 
            else 
            {
            	$this->view->errors = array('Please fix the errors below.');
                $form->populate($formData);
            }
            
	    }
	    
	    $this->view->contact_data = $contact_data;
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		    
		$this->view->headTitle('Edit Contact', 'PREPEND');     	
	}
	
	/**
	 * Contact Add Page
	 * @return void
	 */
	public function addAction()
	{

		if(!$this->perm->check($this->identity, 'manage_company_contacts'))
        {
        	$this->_helper->redirector('index', 'contacts', 'pm');
        	exit;
        }
        		
		$company_id = $this->_getParam("company",FALSE);
		if(!$company_id)
		{
			$company_id = $this->_getParam("company_id",FALSE);
			if(!$company_id)
			{
				$this->_helper->redirector('index','companies', 'pm');
				exit;
			}
		}
		
		$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
		$company_data = $company->getCompanyById($company_id);
		if(!$company_data)
		{
			$this->_helper->redirector('index','companies', 'pm');
			exit;
		}
		
		$contact = new PM_Model_Contacts;
		
		$form = $contact->getContactForm(array(
            'action' => '/pm/contacts/add/company/'.$company_id,
            'method' => 'post',
        ));
		
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				$formData['creator'] = $this->identity;
				if($id = $contact->addContact($formData)){
			    	$this->_flashMessenger->addMessage('Contact Added!');
					$this->_helper->redirector('view','contacts', 'pm', array('id' => $id));
				} else {	
					$this->view->errors = array('Something went wrong...');
				}
				
			} else {
				$this->view->errors = array('Please fix the errors below.');
			}

		}
		
        $this->view->layout_style = 'right';
        $this->view->sidebar = 'dashboard';		
		$this->view->title = FALSE;
		$this->view->headTitle('Add Contact', 'PREPEND');
		$this->view->addAction = TRUE;
		$this->view->company_data = $company_data;

		$this->view->active_sub = $company_data['type'];
		$this->view->form = $form;
		$this->view->id = $company_id;
	}
	
	function removeAction()
	{
		if(!$this->perm->check($this->identity, 'manage_company_contacts'))
        {
        	$this->_helper->redirector('index', 'contacts', 'pm');
        	exit;
        }
        		
		$contacts = new PM_Model_Contacts;
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','contacts');
    		exit;
    	}
    	
    	$this->view->contact = $contacts->getContactById($id);
    	if(!$this->view->contact)
    	{
			$this->_helper->redirector('index','contacts');
			exit;
    	}

    	if($fail)
    	{
			$this->_helper->redirector('view','contacts', 'pm', array('id' => $id));
			exit;   		
    	}
    	
    	if($confirm)
    	{
    	   	if($contacts->removeContact($id))
    		{	
				$this->_flashMessenger->addMessage('Contact Removed');
				$this->_helper->redirector('index','contacts');
				exit;
				
    		}
    	}
    	
    	/*
    	$this->view->project_count = $contacts->getProjectCount($id);
    	$this->view->task_count = $contacts->getTaskCount($id);
    	$this->view->file_count = $contacts->getFileCount($id);
    	*/
    	
		$this->view->title = "Delete Contact: ". $this->view->contact['first_name'].' '.$this->view->contact['last_name'];
		$this->view->headTitle('Delete Contact: '. $this->view->contact['first_name'].' '.$this->view->contact['last_name'], 'PREPEND');
		$this->view->id = $id;    	
	}
}