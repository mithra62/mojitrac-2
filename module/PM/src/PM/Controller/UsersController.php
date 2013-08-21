<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/UsersController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Users Controller
*
* Routes the Users requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/UsersController.php
*/
class Pm_UsersController extends PM_Abstract
{

	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{ 
		parent::preDispatch();
        $this->view->headTitle('Users', 'PREPEND');
        $this->view->layout_style = 'single';
		$this->view->sidebar = 'dashboard';
		$this->view->sub_menu = 'admin';
		$this->view->active_nav = 'admin';
		$this->view->active_sub = 'users';
        $this->view->uri = $this->_request->getPathInfo();
		$this->view->title = FALSE;          
	}

	/**
	 * Main Page
	 * @return void
	 */
	public function indexAction()
	{
        parent::check_permission('view_users_data');
		$users = new PM_Model_Users(new PM_Model_DbTable_Users);
		$this->view->users = $users->getAllUsers();
	}

	/**
	 * User View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->view->active_nav = '';
			$this->view->sub_menu = 'settings';
			$id = $this->identity;
		}
		
		if(!$this->perm->check($this->identity, 'view_users_data'))
        {
        	$this->view->active_nav = '';
        	$this->view->sub_menu = 'settings';
        	$id = $this->identity;
        }		

		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$this->view->user = $user->getUserById($id);
		if(!$this->view->user)
		{
			$this->_helper->redirector('index','users');
			exit;
		}
		
		$this->view->roles = $user->getUserRoles($id);
		
		$this->view->projects = $user->getAssignedProjects($id);

		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
		$this->view->tasks = $task->getTasksByUserId($id, TRUE, TRUE, TRUE);
		
		$file = new PM_Model_Files(new PM_Model_DbTable_Files);
		$this->view->files = $file->getFilesByUserId($id);

		
		$times = new PM_Model_Times;
		$this->view->times = $times->getTimesByUserId($id);

		$this->view->hours = $times->getTotalTimesByUserId($id);
		
		$this->view->headTitle('Viewing User: '. $this->view->user['first_name'].' '.$this->view->user['last_name'], 'PREPEND');
		$this->view->id = $id;
	}

	/**
	 * User Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->view->active_nav = '';
			$this->view->sub_menu = 'settings';
			$id = $this->identity;
		}
		
		if(!$this->perm->check($this->identity, 'manage_users'))
        {
        	$this->view->active_nav = '';
        	$this->view->sub_menu = 'settings';
        	$id = $this->identity;
        }			

		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$form = $user->getUsersForm(array(
            'action' => '/pm/users/edit/',
            'method' => 'post',
		), FALSE, FALSE, FALSE, $this->perm->check($this->identity, 'manage_users'));

		$this->view->id = $id;
		$this->view->add_groups = $this->perm->check($this->identity, 'manage_users');

		$user_data = $user->getUserById($id);
		$user_data['user_roles'] = $user->getUserRolesArr($id);

		$form->populate($user_data);
		 
		$this->view->form = $form;

		if ($this->getRequest()->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{			
				if($user->updateUser($formData, $formData['id']))
				{
					
					if($this->identity != $id && $user_data['email'] != $formData['email'])
					{
						$noti = new PM_Model_Notifications();
						$noti->sendUserAdd($formData, TRUE);
					}
					$this->_flashMessenger->addMessage('User updated!');
					$this->_helper->redirector('view','users', 'pm', array('id' => $id));
				} 
				else 
				{
					$this->view->errors = array('Couldn\'t update user...');
					$form->populate($formData);
				}

			} 
			else 
			{
				$this->view->errors = array('Please fix the errors below.');
				$form->populate($formData);
			}

		}
	  
		$this->view->user_data = $user_data;
		$this->view->layout_style = 'right';
		$this->view->sidebar = 'dashboard';
		$this->view->headTitle('Edit User', 'PREPEND');
	}

	/**
	 * User Add Page
	 * @return void
	 */
	public function addAction()
	{
		
		if(!$this->perm->check($this->identity, 'manage_users'))
        {
			$this->_helper->redirector('view','users', 'pm');
			exit;
        }

		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
		$form = $user->getUsersForm(array(
            'action' => '/pm/users/add/',
            'method' => 'post',
		), TRUE, FALSE, TRUE, TRUE);

		$this->view->form = $form;
		$this->view->addPassword = TRUE;
		
		$roles = new PM_Model_Roles;
		$this->view->user_roles = $roles->getAllRoleNames();

		$this->view->layout_style = 'right';
		$this->view->sidebar = 'dashboard';
		$this->view->headTitle('Add User', 'PREPEND');
		$this->view->addAction = TRUE;		
		
		if ($this->getRequest()->isPost()) {

			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				
				if ($formData['password'] != $formData['confirm_password']) 
				{
					$this->view->errors = array("Password and Confirm Password must match.");
					$this->render('add');
					return;
				}
				
				if($id = $user->addUser($formData))
				{
					$noti = new PM_Model_Notifications();
					$noti->sendUserAdd($formData);
					
					$this->_flashMessenger->addMessage('User Added!');
					$this->_helper->redirector('view','users', 'pm', array('id' => $id));
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
				$form->populate($formData);
			}
		}
	}

	function removeAction()
	{
		
		if(!$this->perm->check($this->identity, 'manage_users'))
        {
			$this->_helper->redirector('view','users', 'pm');
			exit;
        }
        		
		$users = new PM_Model_Users(new PM_Model_DbTable_Users);
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);

		if(!$id)
		{
			$this->_helper->redirector('index','users');
			exit;
		}
		
		if($this->identity == $id)
		{
			$this->_flashMessenger->addMessage('You can\'t remove yourself...');
			$this->_helper->redirector('index','users');
			exit;
		}
		 
		$this->view->user = $users->getUserById($id);
		if(!$this->view->user)
		{
			$this->_helper->redirector('index','users');
			exit;
		}

		if($fail)
		{
			$this->_helper->redirector('view','users', 'pm', array('id' => $id));
			exit;
		}
		 
		if($confirm)
		{
			if($users->removeUser($id))
			{
				$this->_flashMessenger->addMessage('User Removed');
				$this->_helper->redirector('index','users');
				exit;

			} else {
				 
			}
		}
		 
		
		$this->view->projects_owned_count = count($users->getAssignedProjectIds($id));
		$this->view->tasks_owned_count = count($users->getAssignedTasks($id));
		 
		$this->view->headTitle('Delete User: '. $this->view->user['first_name'].' '.$this->view->user['last_name'], 'PREPEND');
		$this->view->id = $id;
	}
}