<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/InvoicesController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Invoices Controller
 *
 * Routes the invoices requests
 *
 * @package 	Companies\Invoices
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/InvoicesController.php
 */
class InvoicesController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
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
	 * Invoice View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('invoice_id');
		if (!$id) {
			return $this->redirect()->toRoute('companies');
		}
		
		$invoice = $this->getServiceLocator()->get('PM\Model\Invoices');
		$view['invoice'] = $invoice->getInvoiceById($id);
		if(!$view['invoice'])
		{
			return $this->redirect()->toRoute('companies');
		}

		$view['id'] = $id;
		
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Contact Edit Page
	 * @return void
	 */
	public function editAction()
	{
	    if(!$this->perm->check($this->identity, 'manage_invoices')) {
        	return $this->redirect()->toRoute('companies');
        }
        		
		$id = $this->params()->fromRoute('invoice_id');
		if (!$id) {
			return $this->redirect()->toRoute('companies');
		}
		
		$invoice = $this->getServiceLocator()->get('PM\Model\Invoices');
		$invoice_data = $invoice->getInvoiceById($id);
		if(!$invoice_data)
		{
			return $this->redirect()->toRoute('companies');
		}

		$form = $this->getServiceLocator()->get('PM\Form\InvoiceForm');
        $form->setData($invoice->getInvoiceById($id));	
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            $form->setInputFilter($invoice->getInputFilter());  
            $form->setData($request->getPost());
            if ($form->isValid($formData)) 
            {
            	if($invoice->updateInvoice($formData->toArray(), $id))
	            {	
			    	$this->flashMessenger()->addMessage('Invoice updated!');
					return $this->redirect()->toRoute('invoices/view', array('invoices_id' => $id));
					        		
            	} 
            	else 
            	{
            		$view['errors'] = array('Couldn\'t update invoice...');
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
	    
	    $view['invoice_data'] = $invoice_data;
		$this->layout()->setVariable('layout_style', 'right'); 
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Invoice Add Page
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|Ambigous <\Zend\View\Model\ViewModel, boolean, array>
	 */
	public function addAction()
	{
		if(!$this->perm->check($this->identity, 'manage_invoices'))
        {
        	return $this->redirect()->toRoute('invoices');
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
		
		$invoice = $this->getServiceLocator()->get('PM\Model\Invoices');
		$form = $this->getServiceLocator()->get('PM\Form\InvoiceForm');
		$form->setData(array('date' => date('Y-m-d')));
        $request = $this->getRequest();
		if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		$formData = $this->getRequest()->getPost();
    		$form->setInputFilter($invoice->getInputFilter());
    		$form->setData($request->getPost());
    		    		
			if ($form->isValid($formData)) {
				$formData['creator'] = $this->identity;
				$invoice_id = $invoice->addInvoice($company_id, $formData->toArray());
				if($invoice_id){
			    	$this->flashMessenger()->addMessage('Invoice Added!');
					return $this->redirect()->toRoute('invoices/view', array('invoice_id' => $invoice_id));
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
		$view['form_action'] = $this->getRequest()->getRequestUri();
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