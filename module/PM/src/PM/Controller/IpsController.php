<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
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
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/IpsController.php
*/
class IpsController extends AbstractPmController
{
	/**
	 * Class preDispatch
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
    
    public function enableAction()
    {
    	$settings = new Model_Settings;
    	$data = array('enable_ip' => '1');
    	if($settings->updateSettings($data))
    	{
	    	$this->_flashMessenger->addMessage('Ip Blocking Enabled!');
			$this->_helper->redirector('index','ips', 'pm');
			exit;
    	}   

		$this->_helper->redirector('index','ips', 'pm');
		exit;    	
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
		if (!$id) {
			$this->_helper->redirector('index','ips');
			exit;
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
					$this->flashMessenger()->addMessage('IP Address Added!');
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
					$this->flashMessenger()->addMessage('IP Address Updated!');
			    	return $this->redirect()->toRoute('ips/view', array('ip_id' => $id));
				} 
				else 
				{
					$view['errors'] = array('Couldn\'t update Ip Address...');
					$form->setData($formData);
				}

			} 
			else 
			{
				$view['errors'] = array('Please fix the errors below.');
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
		$id = $this->params()->fromRoute('ip_id');

		if(!$id)
		{
			$this->_helper->redirector('index','ips');
			exit;
		}
				 
		$ip = $ips->getIpById($id);
		if(!$ip)
		{
			$this->_helper->redirector('index','ips');
			exit;
		}

		$this->view->ip = $ip;
		if($this->settings['enable_ip'] && $ip['ip_raw'] == $_SERVER['REMOTE_ADDR'])
		{
			$this->view->deny = TRUE;
			return;
		}

		if($fail)
		{
			$this->_helper->redirector('view','ips', 'pm', array('id' => $id));
			exit;
		}
		 
		if($confirm)
		{
			if($ips->removeIp($id))
			{
				$this->_flashMessenger->addMessage('Ip Removed');
				$this->_helper->redirector('index','ips');
				exit;

			} 
		}
		$this->view->id = $id;
		$this->view->headTitle('Delete Ip Address: '. $id, 'PREPEND');		
	}
    
}