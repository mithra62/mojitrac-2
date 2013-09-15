<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/UsersController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Users Controller
*
* Routes the Users requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/UsersController.php
*/
class UsersController extends AbstractPmController
{

	/**
	 * Class preDispatch
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );      
	}

	/**
	 * Main Page
	 * @return void
	 */
	public function indexAction()
	{
        if(!$this->perm->check($this->identity, 'view_users_data'))
        {
            return $this->redirect()->toRoute('pm');
        }
        
		$users = $this->getServiceLocator()->get('Application\Model\User');
		$view['users'] = $users->getAllUsers();
		return $view;
	}

	/**
	 * User View Page
	 * @return void
	 */
	public function viewAction()
	{
		$id = $this->params()->fromRoute('user_id');
		if (!$id) 
		{
			$this->layout()->setVariable('active_nav', '');
			$this->layout()->setVariable('sub_menu', 'settings');
			$id = $this->identity;
		}
		
		if(!$this->perm->check($this->identity, 'view_users_data'))
        {
			$this->layout()->setVariable('active_nav', '');
			$this->layout()->setVariable('sub_menu', 'settings');
        	$id = $this->identity;
        }		

		$user = $this->getServiceLocator()->get('Application\Model\User');
		$view['user'] = $user->getUserById($id);
		if(!$view['user'])
		{
			return $this->redirect()->toRoute('pm');
		}
		
		$view['roles'] = $user->getUserRoles($id);
		$view['projects'] = $user->getAssignedProjects($id);

		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$view['tasks'] = $task->getTasksByUserId($id, TRUE, TRUE, TRUE);
		
		$file = $this->getServiceLocator()->get('PM\Model\Files');
		$view['files'] = $file->getFilesByUserId($id);

		
		$times = $this->getServiceLocator()->get('PM\Model\Times');
		$view['times'] = $times->getTimesByUserId($id);

		$view['hours'] = $times->getTotalTimesByUserId($id);
		
		//$this->view->headTitle('Viewing User: '. $this->view->user['first_name'].' '.$this->view->user['last_name'], 'PREPEND');
		$view['id'] = $id;
		return $view;
	}

	/**
	 * User Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('user_id');
		if (!$id) 
		{
			$this->layout()->setVariable('active_nav', '');
			$this->layout()->setVariable('sub_menu', 'settings');
			$id = $this->identity;
		}
		
		if(!$this->perm->check($this->identity, 'view_users_data'))
        {
			$this->layout()->setVariable('active_nav', '');
			$this->layout()->setVariable('sub_menu', 'settings');
        	$id = $this->identity;
        }		

		$user = $this->getServiceLocator()->get('Application\Model\User');
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