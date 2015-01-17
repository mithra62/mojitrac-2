<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/IpsController.php
 */

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
 * PM - Ips Controller
 *
 * Routes the IP Blocker requests
 *
 * @package 	IpLocker
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/src/PM/Controller/IpsController.php
 */
class IpsController extends AbstractPmController
{
	/**
	 * (non-PHPdoc)
	 * @see \PM\Controller\AbstractPmController::onDispatch()
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e ); 
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('active_nav', 'admin');
        $this->layout()->setVariable('sub_menu', 'admin');
        $this->layout()->setVariable('active_sub', 'ips');
		return $e;       
	}
    
	/**
	 * Action for enabling the IP Locker
	 * @return Ambigous <\Zend\Http\Response, \Zend\Stdlib\ResponseInterface>|Ambigous <\Zend\View\Model\ViewModel, boolean, array>
	 */
    public function enableAction()
    {
    	$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$ip = $this->getServiceLocator()->get('PM\Model\Ips');
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
    				return $this->redirect()->toRoute('ips');
    			}
    			
    			$ip->addIp(array('ip' => $request->getServer()->get('REMOTE_ADDR')), $this->identity);
    			
    			$settings = $this->getServiceLocator()->get('Application\Model\Settings');
    			$data = array('enable_ip' => ($this->settings['enable_ip'] == '1' ? '0' : '1'));
    			if($settings->updateSettings($data))
    			{
					$this->flashMessenger()->addMessage($this->settings['enable_ip'] == '1' ? $this->translate('ip_locker_disabled', 'pm') : $this->translate('ip_locker_enabled', 'pm'));
			    	return $this->redirect()->toRoute('ips');
    			}
    		}
    	}

    	$view = array();
    	$view['form'] = $form;
    	$view['ip_block_enabled'] = $this->settings['enable_ip'];
    	return $this->ajaxOutput($view);
    }
    
    public function indexAction()
    {
    	$ips = $this->getServiceLocator()->get('PM\Model\Ips');
    	$view['ip_block_enabled'] = $this->settings['enable_ip'];
    	$view['ips'] = $ips->getAllIps();
    	return $view;
    }
    
	public function viewAction()
	{
		$id = $this->params()->fromRoute('ip_id');
		if (!$id) 
		{	
			return $this->redirect()->toRoute('ips');
		}

		$ips = $this->getServiceLocator()->get('PM\Model\Ips');
		$view['ip'] = $ips->getIpById($id);
		if(!$view['ip'])
		{
			return $this->redirect()->toRoute('ips');
		}
		
		return $this->ajaxOutput($view);
	}    
	
	/**
	 * IP Address Add Page
	 * @return void
	 */
	public function addAction()
	{
		$ip = $this->getServiceLocator()->get('PM\Model\Ips');
		$form = $this->getServiceLocator()->get('PM\Form\IpForm');
		$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($ip->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid($formData))
			{
				$ip_id = $ip->addIp($formData->toArray(), $this->identity);
				if($ip_id)
				{
					$this->flashMessenger()->addMessage($this->translate('ip_address_added', 'pm'));
			    	return $this->redirect()->toRoute('ips/view', array('ip_id' => $ip_id));
				}
			}
		}
		
		$view['form'] = $form;
        $this->layout()->setVariable('layout_style', 'left');
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Ip Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('ip_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('ips');
		}

		$ip = $this->getServiceLocator()->get('PM\Model\Ips');
		$form = $this->getServiceLocator()->get('PM\Form\IpForm');
		
		$ip_data = $ip->getIpById($id);
		if(!$ip_data)
		{
			return $this->redirect()->toRoute('ips');			
		}

		$view = array();
		$view['id'] = $id;
		$ip_data['ip'] = $ip_data['ip_raw'];
		$form->setData($ip_data);
		 
		$view['form'] = $form;
		
		$request = $this->getRequest();
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setInputFilter($ip->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid($formData))
			{
				if($ip->updateIp($formData->toArray(), $formData['id']))
				{
					$this->flashMessenger()->addMessage($this->translate('ip_address_updated', 'pm')); 
			    	return $this->redirect()->toRoute('ips/view', array('ip_id' => $id));
				} 
				else 
				{
					$view['errors'] = array($this->translate('cant_update_ip_address', 'pm'));
					$this->layout()->setVariable('errors', $view['errors']);
					$form->setData($formData);
				}

			} 
			else 
			{
				$view['errors'] = array($this->translate('please_fix_the_errors_below', 'pm')); 
				$this->layout()->setVariable('errors', $view['errors']);
				$form->setData($formData);
			}
		}

		$this->layout()->setVariable('layout_style', 'left');
		$view['ip_data'] = $ip_data;
		return $this->ajaxOutput($view);
	}	
	
	public function removeAction()
	{
		$ips = $this->getServiceLocator()->get('PM\Model\Ips');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		
		$id = $this->params()->fromRoute('ip_id');
		if(!$id)
		{
			return $this->redirect()->toRoute('ips');
		}
				 
		$ip = $ips->getIpById($id);
		if(!$ip)
		{
			return $this->redirect()->toRoute('ips');
		}

		$view = array();
		$view['ip'] = $ip;
		$request = $this->getRequest();
		if($this->settings['enable_ip'] && $ip['ip_raw'] == $request->getServer()->get('REMOTE_ADDR'))
		{
			$this->flashMessenger()->addErrorMessage($this->translate('cant_remove_own_ip'));
			return $this->redirect()->toRoute('ips/view', array('ip_id' => $id));
		}
		
		if ($request->isPost())
		{
			$formData = $this->getRequest()->getPost();
			$form->setData($request->getPost());
			if ($form->isValid($formData))
			{
				$formData = $formData->toArray();
				if(!empty($formData['fail']))
				{
					return $this->redirect()->toRoute('ips/view', array('ip_id' => $id));
				}
				
				if($ips->removeIp($id))
				{
					$this->flashMessenger()->addErrorMessage($this->translate('ip_removed'));
					return $this->redirect()->toRoute('ips');
				}				
			}	
		}
		
		$view['form'] = $form;
		return $this->ajaxOutput($view);	
	}
    
}