<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/OptionsController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Options Controller
*
* Routes the Options requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/OptionsController.php
*/
class OptionsController extends AbstractPmController
{
	/**
	 * Class onDispatch
	 */
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
		parent::check_permission('manage_options');
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('active_nav', 'admin');
		$this->layout()->setVariable('sub_menu', 'admin');
		$this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'options');
		return $e;
	}
    
    public function indexAction()
    {
    	$options = $this->getServiceLocator()->get('PM\Model\Options');
   		$view['options'] = $options->getAllOptions();
   		return $view;
    }
    
	public function viewAction()
	{
		$id = $this->params()->fromRoute('option_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('options');
		}

		$options = $this->getServiceLocator()->get('PM\Model\Options');
		
		$view = array();
		$view['option_info'] = $options->getOptionById($id);
		if(!$view['option_info'])
		{
			return $this->redirect()->toRoute('options');
		}
		
		$view['id'] = $id;
		return $view;
	}    
	
	/**
	 * Option Add Page
	 * @return void
	 */
	public function addAction()
	{
		$options = $this->getServiceLocator()->get('PM\Model\Options');
		$form = $options->getOptionsForm(array(
            'action' => '/pm/options/add',
            'method' => 'post',
        ));
        
       	if ($this->getRequest()->isPost()) 
		{
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				if($id = $options->addOption($formData, $this->identity))
				{
			    	$this->_flashMessenger->addMessage('Option Added!');
					$this->_helper->redirector('index','options', 'pm', array('id' => $id));
					exit;
				}
			}
		}
		
		$this->view->layout_style = 'right';
		$this->view->form = $form;

	}
	
	/**
	 * Option Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->_request->getParam('id', FALSE);
		if (!$id) 
		{
			$this->_helper->redirector('index','ips');
			exit;
		}

		$options = new PM_Model_Options(new PM_Model_DbTable_Options);
		$form = $options->getOptionsForm(array(
            'action' => '/pm/options/edit/',
            'method' => 'post',
		));
		
		$option_data = $options->getOptionById($id);
		if(!$option_data)
		{
			$this->_helper->redirector('index','options');
			exit;			
		}

		$this->view->id = $id;
		$form->populate($option_data);
		$this->view->form = $form;

		if ($this->getRequest()->isPost()) 
		{
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{

				if($options->updateOption($formData, $formData['id']))
				{
					$this->_flashMessenger->addMessage('Option Updated!');
					$this->_helper->redirector('index','options', 'pm', array('id' => $id));
					exit;
					 
				} 
				else 
				{
					$this->view->errors = array('Couldn\'t update Option...');
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
		$this->view->headTitle('Edit Option', 'PREPEND');
	}	
	
	public function removeAction()
	{
		$options = new PM_Model_Options(new PM_Model_DbTable_Options);
		$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);

		if(!$id)
		{
			$this->_helper->redirector('index','options');
			exit;
		}
				 
		$option = $options->getOptionById($id);
		if(!$option)
		{
			$this->_helper->redirector('index','options');
			exit;
		}

		$this->view->option = $option;
		if($fail)
		{
			$this->_helper->redirector('view','options', 'pm', array('id' => $id));
			exit;
		}
		 
		if($confirm)
		{
			if($options->removeOption($id))
			{
				$this->_flashMessenger->addMessage('Option Removed');
				$this->_helper->redirector('index','options');
				exit;

			} 
		}
		$this->view->id = $id;
		$this->view->headTitle('Delete Option: '. $option['name'], 'PREPEND');		
	}
    
}