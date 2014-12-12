<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Controller/AccountsController.php
 */

namespace HostManager\Controller;

use Application\Controller\AbstractController;

/**
 * HostManager - Accounts Controller
 *
 * Handles account routing 
 *
 * @package 	HostManager\Accounts
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Controller/AccountsController.php
 */
class AccountsController extends AbstractController
{  
	/**
	 * (non-PHPdoc)
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
    public function indexAction() 
    {
    	$form = $this->getServiceLocator()->get('HostManager\Form\SignupForm');
    	$request = $this->getRequest();
    	if ($request->isPost())
    	{
    		$account = $this->getServiceLocator()->get('HostManager\Model\Accounts');
    		$user = $this->getServiceLocator()->get('Application\Model\Users');
    		$hash = $this->getServiceLocator()->get('Application\Model\Hash');
    		$company = $this->getServiceLocator()->get('PM\Model\Companies');
    		
			$form->setInputFilter($account->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) 
			{
				$data = $form->getData();
				$account_data = $account->createAccount($data, $user, $company, $hash);
				if($account_data)
				{
					return $this->redirect()->toRoute('account/signup/complete');
				}
			}
    	}
    	
    	$view = array();
    	$view['messages'] = $this->flashMessenger()->getMessages();
        $view['form'] = $form;
        return $view;
    }  
}