<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/ContactsController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Contacts Controller
*
* Routes the contacts requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/ContactsController.php
*/
class ContactsController extends AbstractPmController
{
	/**
	 * Class preDispatch
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        parent::check_permission('view_company_contacts');
		$this->layout()->setVariable('sub_menu', 'companies');
		$this->layout()->setVariable('active_nav', 'companies');
		$this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Companies::types());        
	}
    
    /**
     * Main Page
     * @return void
     */
	public function indexAction()
	{
		
	    $contacts = $this->getServiceLocator()->get('PM\Model\Contacts');
		//$view = $this->_getParam("view",FALSE);
		$view['company'] = FALSE;
		$company_id = $this->params()->fromRoute('company_id');
		if($company_id)
		{
			$company = $this->getServiceLocator()->get('PM\Model\Companies');
			$view['company'] = $company->getCompanyById($company_id);
			if(!$view['company'])
			{
				$company_id = FALSE;
			}
		}
		
		if($company_id) {
			$view['contacts'] = $contacts->getContactsByCompanyId($company_id);
		} else {
			$view['contacts'] = $contacts->getAllContacts($view);
		}
		
		return $view;
	}
	
	/**
	 * Contact View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('contact_id');
		if (!$id) {
			return $this->redirect()->toRoute('companies');
		}
		
		$contact = $this->getServiceLocator()->get('PM\Model\Contacts');
		$view['contact'] = $contact->getContactById($id);
		if(!$view['contact'])
		{
			return $this->redirect()->toRoute('contacts');
		}
			
		//$this->view->title = FALSE;
		//$this->view->headTitle('Viewing Contact: '. $this->view->contact['first_name'].' '.$this->view->contact['last_name'], 'PREPEND');
		$view['id'] = $id;
		
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Contact Edit Page
	 * @return void
	 */
	public function editAction()
	{
	    if(!$this->perm->check($this->identity, 'manage_company_contacts')) {
        	return $this->redirect()->toRoute('contacts');
        }
        		
		$id = $this->params()->fromRoute('contact_id');
		if (!$id) {
			return $this->redirect()->toRoute('contacts');
		}
		
		$contact = $this->getServiceLocator()->get('PM\Model\Contacts');
		$contact_data = $contact->getContactById($id);
		if(!$contact_data)
		{
			return $this->redirect()->toRoute('contacts');
		}

		$form = $this->getServiceLocator()->get('PM\Form\ContactForm');
        $form->setData($contact->getContactById($id));	
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($contact->getInputFilter());  
            $form->setData($request->getPost());
            if ($form->isValid($formData)) 
            {
            	if($contact->updateContact($formData->toArray(), $id))
	            {	
			    	$this->flashMessenger()->addMessage('Contact updated!');
					return $this->redirect()->toRoute('contacts/view', array('contact_id' => $id));
					        		
            	} 
            	else 
            	{
            		$view['errors'] = array('Couldn\'t update contact...');
            		$form->setData($formData);
            	}
                
            } 
            else 
            {
            	$view['errors'] = array('Please fix the errors below.');
                $form->setData($formData);
            }
            
	    }
	    
	    $view['id'] = $id;
	    $view['form'] = $form;	    
	    
	    $view['contact_data'] = $contact_data;
		$this->layout()->setVariable('layout_style', 'right');     
		//$this->view->headTitle('Edit Contact', 'PREPEND');     	
		
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Contact Add Page
	 * @return void
	 */
	public function addAction()
	{

		if(!$this->perm->check($this->identity, 'manage_company_contacts'))
        {
        	return $this->redirect()->toRoute('contacts');
        }
        		
		$company_id = $this->params()->fromRoute('company_id');
		if(!$company_id)
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$company = $this->getServiceLocator()->get('PM\Model\Companies');
		$company_data = $company->getCompanyById($company_id);
		if(!$company_data)
		{
			return $this->redirect()->toRoute('companies');
		}
		
		$contact = $this->getServiceLocator()->get('PM\Model\Contacts');
		
		$form = $this->getServiceLocator()->get('PM\Form\ContactForm');
        $request = $this->getRequest();
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($contact->getInputFilter());
    		$form->setData($request->getPost());
    		    		
			if ($form->isValid($formData)) {
				$formData['creator'] = $this->identity;
				$contact_id = $contact->addContact($formData->toArray());
				if($contact_id){
			    	$this->flashMessenger()->addMessage('Contact Added!');
					return $this->redirect()->toRoute('contacts/view', array('contact_id' => $contact_id));
				} else {	
					$view['errors'] = array('Something went wrong...');
				}
				
			} else {
				$view['errors'] = array('Please fix the errors below.');
			}

		}
		$view['addAction'] = TRUE;
		$view['company_data'] = $company_data;

		$this->layout()->setVariable('active_sub', $company_data['type']);
		$this->layout()->setVariable('layout_style', 'left');
		$view['form'] = $form;
		$view['id'] = $company_id;
		return $this->ajaxOutput($view);
	}
	
	public function removeAction()
	{
		if(!$this->perm->check($this->identity, 'manage_company_contacts'))
        {
        	return $this->redirect()->toRoute('contacts');
        }
        		
		$contacts = $this->getServiceLocator()->get('PM\Model\Contacts');
		$id = $this->params()->fromRoute('contact_id');
		$confirm = $this->params()->fromPost('confirm');
		$fail = $this->params()->fromPost('fail');
		
    	if(!$id)
    	{
    		return $this->redirect()->toRoute('contacts');
    	}
    	
    	$view['contact'] = $contacts->getContactById($id);
    	if(!$view['contact'])
    	{
			return $this->redirect()->toRoute('contacts');
    	}

    	if($fail)
    	{
			return $this->redirect()->toRoute('contacts/view', array('contact_id' => $id));
    	}
    	
    	if($confirm)
    	{
    	   	if($contacts->removeContact($id))
    		{	
				$this->flashMessenger()->addMessage('Contact Removed');
				return $this->redirect()->toRoute('companies/view', array('company_id' => $view['contact']['company_id']));
    		}
    	}
    	
		$view['title'] = "Delete Contact: ". $this->view->contact['first_name'].' '.$this->view->contact['last_name'];
		//$this->view->headTitle('Delete Contact: '. $this->view->contact['first_name'].' '.$this->view->contact['last_name'], 'PREPEND');
		$view['id'] = $id;
		return $this->ajaxOutput($view);
	}
}