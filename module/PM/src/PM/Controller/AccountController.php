<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/AccountController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Account Controller
*
* Routes the user account requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/AccountController.php
*/
class AccountController extends AbstractPmController
{
	/**
	 * Class preDispatch
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        //parent::check_permission('view_projects');
        //$this->layout()->setVariable('layout_style', 'single');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('sub_menu', 'account');
        $this->layout()->setVariable('active_nav', 'account');
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		    
		return $e;
	}

    /**
     * The default section page
     */
    public function indexAction()
    {
    	$view['sub_menu'] = 'settings';
    	return $view;
    }
    
    /**
     * Handles modifying a password
     */
    public function passwordAction()
    {	
    	$user = $this->getServiceLocator()->get('Application\Model\Users');
		$form = $this->getServiceLocator()->get('Application\Form\PasswordForm');
		$hash = $this->getServiceLocator()->get('Application\Model\Hash');
		$form = $form->confirmField();
		$request = $this->getRequest();
        if ($request->isPost()) {
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($user->getPasswordInputFilter($this->identity, $hash));
			$form->setData($formData);
			if ($form->isValid($formData)) {
				if($user->changePassword($this->identity, $formData['new_password'])){
					$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
			    	$this->flashMessenger()->addMessage($translate('password_changed', 'pm'));
					return $this->redirect()->toRoute('account/password');		
				}
			}   
		} 
		
		$this->layout()->setVariable('layout_style', 'right');
		$this->layout()->setVariable('active_sub', 'password');	
		$this->layout()->setVariable('sub_menu', 'settings');	
		$view['form'] = $form;
		return $view;
    }
    
    public function prefsAction()
    {
    	$view['sub_menu'] = 'settings';
    	$view['active_sub'] = 'prefs'; 
		$view['layout_style'] = 'right';
		$view['sidebar'] = 'dashboard';
		
		$request = $this->getRequest();
		$form = $this->getServiceLocator()->get('Application\Form\PrefsForm'); 
        if ($request->isPost()) {
			$ud = $this->getServiceLocator()->get('Application\Model\User\Data');
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($ud->getInputFilter());
			$form->setData($formData);
			if ($form->isValid($formData)) {
				if($ud->updateUserData($formData->toArray(), $this->identity)) {
					$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
			    	$this->flashMessenger()->addMessage($translate('prefs_updated', 'pm'));
					return $this->redirect()->toRoute('account/prefs');			
				}
			}   
		}        

		$form->setData($this->prefs);
        $view['form'] = $form;
        $view['layout_style'] = 'right';
        $this->layout()->setVariable('layout_style', 'right');
        $this->layout()->setVariable('active_sub', 'prefs');
        return $view;
    }
}