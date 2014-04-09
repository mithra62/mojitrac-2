<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/RolesController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Roles Controller
*
* Routes the Roles requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/RolesController.php
*/
class RolesController extends AbstractPmController
{

	/**
	 * Class preDispatch
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
        //$this->layout()->setVariable('layout_style', 'single');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'roles');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'None');
		return parent::onDispatch( $e );
	}

	/**
	 * Main Page
	 * @return void
	 */
	public function indexAction()
	{

		$roles = $this->getServiceLocator()->get('Application\Model\Roles');
		$view['roles'] = $roles->getAllRoles();
		return $view;
	}

	/**
	 * Role View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('role_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('roles');
		}

		$roles = $this->getServiceLocator()->get('Application\Model\Roles');
		$view['role'] = $roles->getRoleById($id);
		if(!$view['role'])
		{
			return $this->redirect()->toRoute('roles');
		}
		
		$view['users'] = $roles->getUsersOnRole($id);
		$view['role_permissions'] = $roles->getRolePermissions($id);
		$view['permissions'] = $roles->getAllPermissions();
		$view['id'] = $id;
		return $view;
	}

	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('role_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('roles');
		}

		$role = $this->getServiceLocator()->get('Application\Model\Roles');
		$form = $this->getServiceLocator()->get('Application\Form\RolesForm');
		
		$role_data = $role->getRoleById($id);
		$role_perms = $role->getRolePermissions($id, 'assoc');
		$role_data = array_merge($role_data, $role_perms);
		
		$view['permissions'] = $role->getAllPermissions();

		$view['id'] = $id;

		$form->setData($role_data);
		 
		$view['form'] = $form;
		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {

				if($role->updateRole($formData, $formData['id']))
				{
					$this->_flashMessenger->addMessage('Role updated!');
					$this->_helper->redirector('view','roles', 'pm', array('id' => $id));
					 
				} 
				else 
				{
					$this->view->errors = array('Couldn\'t update role...');
					$form->populate($formData);
				}

			} 
			else 
			{
				$this->view->errors = array('Please fix the errors below.');
				$form->populate($formData);
			}
		}
	  
		return $view;
	}

	/**
	 * Contact Add Page
	 * @return void
	 */
	public function addAction()
	{
		$role = new PM_Model_Roles;
		$form = $role->getRolesForm(array(
            'action' => '/pm/roles/add/',
            'method' => 'post',
		));
		
		$perms = new PM_Model_Roles;
		$this->view->permissions = $perms->getAllPermissions();
		if ($this->getRequest()->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				if($id = $role->addRole($formData))
				{
					$this->_flashMessenger->addMessage('Role Added!');
					$this->_helper->redirector('view', 'roles', 'pm', array('id' => $id));
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

		$this->view->addPassword = TRUE;

		$this->view->layout_style = 'right';
		$this->view->sidebar = 'dashboard';
		$this->view->headTitle('Add Role', 'PREPEND');
		$this->view->addAction = TRUE;
		$this->view->form = $form;
	}

	function removeAction()
	{
		$role = new PM_Model_Roles;
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);

		if(!$id)
		{
			$this->_helper->redirector('index','roles');
			exit;
		}
		
		//don't allow deletion of the user or administrator permissions.
		$deny_remove = FALSE;
		if($id == '1' || $id == '2')
		{
			$deny_remove = TRUE;
			$this->view->deny_remove = TRUE;
		}
		 
		$this->view->role = $role->getRoleById($id);
		if(!$this->view->role)
		{
			$this->_helper->redirector('index','roles');
			exit;
		}

		if($fail)
		{
			$this->_helper->redirector('view','roles', 'pm', array('id' => $id));
			exit;
		}
		 
		if($confirm && !$deny_remove)
		{
			if($role->removeRole($id))
			{
				$this->_flashMessenger->addMessage('Role Removed');
				$this->_helper->redirector('index','roles');
				exit;

			} 
		}
		 
		/*
		$this->view->projects_owned_count = $users->getProjectCount($id);
		$this->view->tasks_owned_count = $users->getTaskCount($id);
		$this->view->files_owned_count = $users->getFileCount($id);
		*/
		 
		$this->view->headTitle('Delete Role: '. $this->view->user['name'], 'PREPEND');
		$this->view->id = $id;
	}
}