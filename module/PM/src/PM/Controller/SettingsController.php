<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/SettingsController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Settings Controller
*
* Routes the Home requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/SettingsController.php
*/
class SettingsController extends AbstractPmController
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
        $this->layout()->setVariable('sub_menu', 'settings');
        $this->layout()->setVariable('active_nav', 'settings');
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		    
		return $e;
	}
    
    /**
     * Handles managing the system emails
     */
    public function emailAction()
    {
        $this->view->sub_menu = 'admin';
        $this->view->active_nav = 'admin';
		$this->view->active_sub = 'emails';
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
    	$user = $this->getServiceLocator()->get('Application\Model\User');
		$form = $this->getServiceLocator()->get('Application\Form\PasswordForm');
		$hash = $this->getServiceLocator()->get('Application\Model\Hash');
        
		$request = $this->getRequest();
        if ($request->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($user->getPasswordInputFilter($this->identity, $hash));
			$form->setData($formData);
			if ($form->isValid($formData)) 
			{
				if($user->changePassword($this->identity, $formData['new_password']))
				{
			    	$this->flashMessenger()->addMessage('Password changed!');
					return $this->redirect()->toRoute('settings/password');		
				}
			}   
		} 
		
		$this->layout()->setVariable('layout_style', 'right');
		$this->layout()->setVariable('active_sub', 'password');	
		$this->layout()->setVariable('sub_menu', 'settings');	
		$view['form'] = $form;
		return $view;
    }
    
    /**
     * Handles the user notifications
     */
    public function notificationsAction()
    {
    	$this->view->sub_menu = 'settings';
    	$this->view->active_sub = 'notifications';
    }
    
    public function prefsAction()
    {
    	$view['sub_menu'] = 'settings';
    	$view['active_sub'] = 'prefs'; 
		$view['layout_style'] = 'right';
		$view['sidebar'] = 'dashboard';
		
		$request = $this->getRequest();
		$form = $this->getServiceLocator()->get('Application\Form\PrefsForm'); 
        if ($request->isPost()) 
		{
			$ud = $this->getServiceLocator()->get('Application\Model\User\Data');
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($ud->getInputFilter());
			$form->setData($formData);
			if ($form->isValid($formData)) 
			{
				if($ud->updateUserData($formData->toArray(), $this->identity))
				{
			    	$this->flashMessenger()->addMessage('Preferences updated!');
					return $this->redirect()->toRoute('settings/prefs');			
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