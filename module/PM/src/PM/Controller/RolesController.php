<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/RolesController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Roles Controller
*
* Routes the Roles requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/RolesController.php
*/
class Pm_RolesController extends PM_Abstract
{

	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::check_permission('manage_roles');
        $this->view->headTitle('User Roles', 'PREPEND');
		$this->view->layout_style = 'single';
		$this->view->sidebar = 'dashboard';
		$this->view->sub_menu = 'admin';
		$this->view->active_nav = 'admin';
		$this->view->active_sub = 'roles';
		$this->view->title = FALSE;          
	}

	/**
	 * Main Page
	 * @return void
	 */
	public function indexAction()
	{

		$roles = new PM_Model_Roles;
		$this->view->roles = $roles->getAllRoles();
	}

	/**
	 * Role View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) {
			$this->_helper->redirector('index','users');
			exit;
		}

		$roles = new PM_Model_Roles;
		$this->view->role = $roles->getRoleById($id);
		if(!$this->view->role)
		{
			$this->_helper->redirector('index','roles');
			exit;
		}
		
		$this->view->users = $roles->getUsersOnRole($id);
		$this->view->role_permissions = $roles->getRolePermissions($id);
		$this->view->permissions = $roles->getAllPermissions();
		$this->view->id = $id;
		$this->view->headTitle('Viewing Role', 'PREPEND');
	}

	/**
	 * Company Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) {
			$this->_helper->redirector('index','ROLES');
		}

		$role = new PM_Model_Roles;
		$form = $role->getRolesForm(array(
            'action' => '/pm/roles/edit/',
            'method' => 'post',
		));
		
		$role_data = $role->getRoleById($id);
		$role_perms = $role->getRolePermissions($id, 'assoc');
		$role_data = array_merge($role_data, $role_perms);
		
		$perms = new PM_Model_Roles;
		$this->view->permissions = $perms->getAllPermissions();

		$this->view->id = $id;

		$form->populate($role_data);
		 
		$this->view->form = $form;

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
	  
		$this->view->layout_style = 'right';
		$this->view->sidebar = 'dashboard';
		$this->view->headTitle('Edit Role', 'PREPEND');
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