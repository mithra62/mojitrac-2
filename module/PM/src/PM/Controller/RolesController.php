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
        $this->layout()->setVariable('active_nav', 'admin');
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
        $this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'roles');
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
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_nav', 'admin');
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
		$this->layout()->setVariable('sub_menu', 'admin');
		$this->layout()->setVariable('active_nav', 'admin');
		return $view;
	}

	/**
	 * User Role Edit Page
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
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		
		$view['permissions'] = $role->getAllPermissions();
		$view['id'] = $id;
		$form->setData($role_data);
		$request = $this->getRequest();
		if ($request->isPost()) 
		{
			$formData = $request->getPost();
            $form->setInputFilter($role->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid($formData)) 
			{	
				$formData = $formData->toArray();
				if($role->updateRole($formData, $formData['id']))
				{
					$this->flashMessenger()->addMessage($translate('role_updated', 'pm'));
					return $this->redirect()->toRoute('roles/view', array('role_id' => $id));					
					 
				} 
				else 
				{
					$view['errors'] = array($translate('update_role_fail', 'pm'));
					$form->setData($formData);
				}

			} 
			else 
			{
				$view['errors'] = array($translate('please_fix_the_errors_below', 'pm'));
				$form->setData($formData);
			}
		}

		$view['form'] = $form;
		$this->layout()->setVariable('layout_style', 'left');
		$this->layout()->setVariable('sub_menu', 'admin');
		$this->layout()->setVariable('active_nav', 'admin');
		return $view;
	}

	/**
	 * User Role Add Page
	 * @return void
	 */
	public function addAction()
	{
		$role = $this->getServiceLocator()->get('Application\Model\Roles');
		$form = $this->getServiceLocator()->get('Application\Form\RolesForm');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		
		$view['permissions'] = $role->getAllPermissions();
		$request = $this->getRequest();
		if ($request->isPost()) 
		{
			$formData = $request->getPost();
            $form->setInputFilter($role->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid($formData)) 
			{	
				$formData = $formData->toArray();
				$role_id = $id = $role->addRole($formData);
				if($role_id)
				{
					$this->flashMessenger()->addMessage($translate('role_added', 'pm'));
					return $this->redirect()->toRoute('roles/view', array('role_id' => $role_id));					
				} 
				else 
				{
					$view['errors'] = array($translate('something_went_wrong', 'pm'));
				}

			} 
			else 
			{
				$view['errors'] = array($translate('please_fix_the_errors_below', 'pm'));
			}
		}

		$view['form'] = $form;
		$this->layout()->setVariable('layout_style', 'left');
		$this->layout()->setVariable('sub_menu', 'admin');
		$this->layout()->setVariable('active_nav', 'admin');
		
		return $view;
	}

	public function removeAction()
	{
		$role = $this->getServiceLocator()->get('Application\Model\Roles');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		$id = $this->params()->fromRoute('role_id');
		if(!$id)
		{
			return $this->redirect()->toRoute('roles');
		}
		
		//don't allow deletion of the user or administrator permissions.
		$deny_remove = FALSE;
		if($id == '1' || $id == '2')
		{
			$deny_remove = TRUE;
			$view['deny_remove'] = TRUE;
		}
		 
		$view['role'] = $role->getRoleById($id);
		if(!$view['role'])
		{
			return $this->redirect()->toRoute('roles');
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
					return $this->redirect()->toRoute('roles/view', array('role_id' => $id));
				}
			
				if($role->removeRole($id))
				{
					$this->flashMessenger()->addMessage($translate('role_removed', 'pm'));
					return $this->redirect()->toRoute('roles');
				} 
			}
		}
		
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);
	}
}