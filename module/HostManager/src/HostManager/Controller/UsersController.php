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
		$invite = $this->getServiceLocator()->get('HostManager\Model\Account\Invites');
		
		$view['invites'] = $invite->getAccountInvites();
		$view['users'] = $users->getAccountUsers();
		return $view;
	}
	
	public function inviteAction()
	{
		if(!$this->perm->check($this->identity, 'manage_users'))
        {
			return $this->redirect()->toRoute('users');  
        }

        $user = $this->getServiceLocator()->get('HostManager\Model\Users');
        $invite = $this->getServiceLocator()->get('HostManager\Model\Account\Invites');
		$form = $this->getServiceLocator()->get('HostManager\Form\InviteForm');

		$form = $form->rolesFields($user->roles);
		$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
            $form->setInputFilter($invite->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid($formData))
			{
				//check if this user exists already
				$formData = $formData->toArray();
				$user_data = $user->getUserByEmail($formData['email']);
				$hash = $this->getServiceLocator()->get('Application\Model\Hash');
				if($user_data)
				{
					//just process the invite
					if($invite->addInvite($user_data['id'], $hash))
					{
						$this->flashMessenger()->addMessage($this->translate('invite_sent', 'hm'));
						return $this->redirect()->toRoute('users');
					}
				}
				else 
				{
					//create the user
					$formData['password'] = $hash->guidish();
					$user_id = $id = $user->addUser($formData, $hash);
					if($user_id)
					{
						if($invite->addInvite($user_id, $hash))
						{
							$this->flashMessenger()->addMessage($this->translate('invite_sent', 'hm'));
							return $this->redirect()->toRoute('users');
						}
					}
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
	 * (non-PHPdoc)
	 * @see \PM\Controller\UsersController::addAction()
	 */
	public function addAction()
	{
		return $this->redirect()->toRoute('users');
	}

	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\UsersController::removeAction()
	 */
	public function removeAction()
	{
		if(!$this->perm->check($this->identity, 'manage_users'))
        {
			return $this->redirect()->toRoute('users'); 
        }

        $view = array();
		$user = $this->getServiceLocator()->get('HostManager\Model\Users');
		$account = $this->getServiceLocator()->get('HostManager\Model\Accounts');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$id = $this->params()->fromRoute('user_id');
		if(!$id)
		{
			return $this->redirect()->toRoute('users'); 
		}
		
		if($this->identity == $id && !$this->getRequest()->isXmlHttpRequest())
		{
			$this->flashMessenger()->addMessage($this->translate('user_cant_remove_self', 'pm'));
			return $this->redirect()->toRoute('users/view', array('user_id' => $id));  
		}
		 
		$view['user'] = $user->getUserById($id);
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

				if($account->removeUserFromAccount($id))
				{
					$this->flashMessenger()->addMessage($this->translate('user_removed', 'pm'));
					return $this->redirect()->toRoute('users');
				}
			}
		}
		 
		
		$view['projects_owned_count'] = count($user->getAssignedProjectIds($id));
		$view['tasks_owned_count'] = count($user->getOpenAssignedTasks($id));
		$view['id'] = $id;
		$view['identity'] = $this->identity;
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
}