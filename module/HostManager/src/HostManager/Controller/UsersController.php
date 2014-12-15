<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Controller/UsersController.php
 */

namespace HostManager\Controller;

use PM\Controller\UsersController AS PmUsers;

/**
 * HostManager - Users Controller
 *
 * Routes the Users requests with an account concept in mind
 *
 * @package 	Users
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Controller/UsersController.php
 */
class UsersController extends PmUsers
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch($e);
        $this->layout('layout/pm');	
		return $e;
	}

	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\UsersController::indexAction()
	 */
	public function indexAction()
	{
        if(!$this->perm->check($this->identity, 'view_users_data'))
        {
            return $this->redirect()->toRoute('pm');
        }
        
		$users = $this->getServiceLocator()->get('HostManager\Model\Users');
		$view['users'] = $users->getAccountUsers();
		return $view;
	}
	
	public function inviteAction()
	{
		if(!$this->perm->check($this->identity, 'manage_users'))
        {
			return $this->redirect()->toRoute('users');  
        }

        $users = $this->getServiceLocator()->get('HostManager\Model\Users');
		$form = $this->getServiceLocator()->get('HostManager\Form\InviteForm');

		$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid($formData))
			{
				echo 'fdsa';
				exit;
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('users/view', array('user_id' => $id));
				}
		
				if($user->removeUser($id))
				{
					$this->flashMessenger()->addMessage($this->translate('user_removed', 'pm'));
					return $this->redirect()->toRoute('users');
				}
			}
		}
		
		$this->layout()->setVariable('layout_style', 'left');
		$view = array();
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}

	/**
	 * User Edit Page
	 * @return array
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

		$user = $this->getServiceLocator()->get('Application\Model\Users');
		$user_form = $this->getServiceLocator()->get('Application\Form\UsersForm');
		$roles = $this->getServiceLocator()->get('Application\Model\Roles');

		$view['id'] = $id;
		$view['add_groups'] = $this->perm->check($this->identity, 'manage_users');

		$user_data = $user->getUserById($id);
		$user_data['user_roles'] = $view['user_roles'] = $user->getUserRolesArr($id);
		
		$user_form->rolesFields($roles);
		$user_form->setData($user_data);
		 
		$view['form'] = $user_form;

		$request = $this->getRequest();
		if ($request->isPost()) 
		{
			$formData = $request->getPost();
            $user_form->setInputFilter($user->getEditInputFilter());
			$user_form->setData($request->getPost());
			if ($user_form->isValid($formData)) 
			{		
				$formData = $formData->toArray();
				if($user->updateUser($formData, $formData['id']))
				{	
					$this->flashMessenger()->addMessage($this->translate('user_updated', 'pm'));
					return $this->redirect()->toRoute('users/view', array('user_id' => $id));					
				} 
				else 
				{
					$view['errors'] = array($this->translate('something_went_wrong', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
					$user_form->setData($formData);
				}

			} 
			else 
			{
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
				$user_form->setData($formData);
			}

		}
	  
		$view['user_data'] = $user_data;
		$this->layout()->setVariable('layout_style', 'left');
		return $view;
	}

	/**
	 * User Add Page
	 * @return void
	 */
	public function addAction()
	{
		if(!$this->perm->check($this->identity, 'manage_users'))
        {
			return $this->redirect()->toRoute('users');  
        }

		$user = $this->getServiceLocator()->get('Application\Model\Users');
		$user_form = $this->getServiceLocator()->get('Application\Form\UsersForm');
		$roles = $this->getServiceLocator()->get('Application\Model\Roles');
		$hash = $this->getServiceLocator()->get('Application\Model\Hash');
		
		$view['form'] = $user_form->registrationForm()->rolesFields($roles);
		$view['addPassword'] = TRUE;
		$view['user_roles'] = $roles->getAllRoleNames();
		$view['layout_style'] = 'right';
		$view['sidebar'] = 'dashboard';
		$view['addAction'] = TRUE;		
		$request = $this->getRequest();
		if ($request->isPost()) {

			$formData = $request->getPost();
            $user_form->setInputFilter($user->getRegistrationInputFilter());
			$user_form->setData($request->getPost());
			if ($user_form->isValid($formData)) 
			{
				$user_id = $id = $user->addUser($formData->toArray(), $hash, $roles);
				if($user_id)
				{	
					$this->flashMessenger()->addMessage($this->translate('user_added', 'pm'));
					return $this->redirect()->toRoute('users/view', array('user_id' => $id));  
				} 
				else 
				{
					$view['errors'] = array($this->translate('something_went_wrong', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
				}

			} 
			else 
			{
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm'));
				$this->layout()->setVariable('errors', $view['errors']);
				$user_form->setData($formData);
			}
		}
        $this->layout()->setVariable('layout_style', 'left');
		return $view;
	}

	/**
	 * The Remove User Action
	 * @return array
	 */
	public function removeAction()
	{
		if(!$this->perm->check($this->identity, 'manage_users'))
        {
			return $this->redirect()->toRoute('users'); 
        }

        $view = array();
		$user = $this->getServiceLocator()->get('HostManager\Model\Users');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->params()->fromRoute('user_id');
		if(!$id)
		{
			return $this->redirect()->toRoute('users'); 
		}
		
		if($this->identity == $id)
		{
			$this->flashMessenger()->addMessage($this->translate('user_cant_remove_self', 'pm'));
			return $this->redirect()->toRoute('users/view', array('user_id' => $id));  
		}
		 
		$view['user'] = $user->getUserById($id.'f');
		if(!$view['user'])
		{
			return $this->redirect()->toRoute('users'); 
		}

	    $request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid($formData))
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('users/view', array('user_id' => $id));
				}

				if($user->removeUser($id))
				{
					$this->flashMessenger()->addMessage($this->translate('user_removed', 'pm'));
					return $this->redirect()->toRoute('users');
				}
			}
		}
		 
		
		$view['projects_owned_count'] = count($user->getAssignedProjectIds($id));
		$view['tasks_owned_count'] = count($user->getOpenAssignedTasks($id));
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
}