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
		return $this->ajaxOutput($view);
	}    
	
	/**
	 * Option Add Page
	 * @return void
	 */
	public function addAction()
	{
		$options = $this->getServiceLocator()->get('PM\Model\Options');
		$form = $this->getServiceLocator()->get('PM\Form\OptionForm');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
        
		$request = $this->getRequest();
		if ($request->isPost()) 
		{
			$formData = $request->getPost();
            $form->setInputFilter($options->getInputFilter($translate));
			$form->setData($request->getPost());
			if ($form->isValid($formData)) 
			{
				$option_id = $options->addOption($formData->toArray(), $this->identity);
				if($option_id)
				{
					$this->flashMessenger()->addMessage($translate('option_added', 'pm'));
					return $this->redirect()->toRoute('options/view', array('option_id' => $option_id));
				}
			}
		}
		
		$view['form'] = $form;
		$view['form_action'] = $this->getRequest()->getRequestUri();
        $this->layout()->setVariable('layout_style', 'right');
		return $this->ajaxOutput($view);
	}
	
	/**
	 * Option Edit Page
	 * @return void
	 */
	public function editAction()
	{
		$id = $this->params()->fromRoute('option_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('options');
		}

		$options = $this->getServiceLocator()->get('PM\Model\Options');
		$form = $this->getServiceLocator()->get('PM\Form\OptionForm');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		
		$option_data = $options->getOptionById($id);
		if(!$option_data)
		{
			return $this->redirect()->toRoute('options');		
		}
	
		$form->setData($option_data);
		$request = $this->getRequest();
		if ($request->isPost()) 
		{
			$formData = $request->getPost();
            $form->setInputFilter($options->getInputFilter($translate));
			$form->setData($request->getPost());
			if ($form->isValid($formData)) 
			{
				if($options->updateOption($formData->toArray(), $formData['id']))
				{
					$this->flashMessenger()->addMessage($translate('option_updated', 'pm'));
					return $this->redirect()->toRoute('options/view', array('option_id' => $id));
					 
				} 
				else 
				{
					$view['errors'] = array('Couldn\'t update Option...');
					$form->setData($formData);
				}

			} 
			else 
			{
				$view['errors'] = array($translate('please_fix_the_errors_below', 'pm'));
				$form->setData($formData);
			}
		}
		
		$view = array();
		$view['id'] = $id;
		$view['form'] = $form;
		$view['form_action'] = $this->getRequest()->getRequestUri();
		$this->layout()->setVariable('layout_style', 'right');
				
		return $this->ajaxOutput($view);
	}	
	
	public function removeAction()
	{
		$options = $this->getServiceLocator()->get('PM\Model\Options');
		$form = $this->getServiceLocator()->get('PM\Form\ConfirmForm');
		$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('_');
		
		$id = $this->params()->fromRoute('option_id');
		if (!$id) 
		{
			return $this->redirect()->toRoute('options');
		}
			 
		$option = $options->getOptionById($id);
		if(!$option)
		{
			return $this->redirect()->toRoute('options');
		}

		$view['option'] = $option;
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
					return $this->redirect()->toRoute('options/view', array('option_id' => $id));
				}
				
				if($options->removeOption($id))
				{
					$this->flashMessenger()->addMessage($translate('option_removed', 'pm'));
					return $this->redirect()->toRoute('options');
				} 
			}
		}
		
		$view['id'] = $id;
		$view['form'] = $form;
		return $this->ajaxOutput($view);			
	}
    
}