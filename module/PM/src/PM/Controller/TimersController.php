<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/TimersController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Timers Controller
*
* Routes the Timers requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/TimersController.php
*/
class TimersController extends AbstractPmController
{
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
		parent::check_permission('track_time');
		//$this->layout()->setVariable('layout_style', 'single');
		$this->layout()->setVariable('sidebar', 'dashboard');
		$this->layout()->setVariable('sub_menu', 'tasks');
		$this->layout()->setVariable('active_nav', 'timers');
        $this->layout()->setVariable('sub_menu_options', \PM\Model\Options\Projects::status());
		$this->layout()->setVariable('uri', $this->getRequest()->getRequestUri());
		$this->layout()->setVariable('active_sub', 'None');
	
		return $e;
	}
    
    public function indexAction()
    {
    	$date = $this->_request->getParam('date', date('F Y'));
    }
    
    public function viewAction()
    {	
    	$id = $this->params()->fromRoute('id');
    	$type = $this->params()->fromRoute('type');
    	$view = array('type' => $type, 'id' => $id);
    	
    	$where = array();
    	if($type == 'company')
    	{
    		parent::check_permission('view_companies');
		    $company_id = $id;
    		$company = $this->getServiceLocator()->get('PM\Model\Companies');
    		$company_data = $company->getCompanyById($company_id);
    		if(!$company_data)
    		{
    			return $this->redirect()->toRoute('companies');
    		}
    		
    		$view['company'] = $company_data;
    	}
    	 
    	if($type == 'project')
    	{
		    $project_id = $id;
			$project = $this->getServiceLocator()->get('PM\Model\Projects');
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
			    return $this->redirect()->toRoute('projects');
			}
				
    		if(!$project->isUserOnProjectTeam($this->identity, $project_id) && !$this->perm->check($this->identity, 'manage_time'))
    		{
    			return $this->redirect()->toRoute('projects');
    		}

    		$view['project'] = $project_data;
    	}
    	
    	if($type == 'task')
    	{
    		$task_id = $id;
    		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    		$project = $this->getServiceLocator()->get('PM\Model\Projects');
    		$task_data = $task->getTaskById($task_id);
    		if(!$task_data)
    		{
    			return $this->redirect()->toRoute('tasks');
    		}
    			
    		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_time'))
    		{
    			return $this->redirect()->toRoute('pm');
    		}

    		$view['task'] = $task_data;
    	}    	
    	
    	return $this->ajaxOutput($view);
    }
    
    public function removeAction()
    {
		$this->timer->clearTimerData($this->identity);
		$this->_flashMessenger->addMessage('Timer Stopped!');
		$this->_helper->redirector('index','index', 'pm');
		exit;    	
    }
    
    public function startAction()
    {
    	if ($this->getRequest()->isPost()) 
        { 
    		$id = $this->params()->fromRoute('id');
    		$type = $this->params()->fromRoute('type');
    		$timer = $this->getServiceLocator()->get('PM\Model\Timers');
        	if($type == 'task')
        	{
        		$timer_data = $timer->startTaskTimer($this->identity, $id);
        	} 
        	elseif($type == 'project')
        	{
        		$timer_data = $timer->startProjectTimer($this->identity, $id);
        	}
        	elseif($type == 'company')
        	{
        		$timer_data = $timer->startCompanyTimer($this->identity, $id);
        	}
        	
        	
        	if($timer_data)
        	{
			    $this->_flashMessenger->addMessage('Timer Started!');
				$this->_helper->redirector('view','tasks', 'pm', array('id' => $this->task_id));  
				exit; 
        	}
        	else
        	{
        		$this->view->errors = array('Couldn\'t start timer...');
        	}
        }  	
    }
    
    public function stopAction()
    {
    	if($this->prefs['timer_data'] == '')
    	{
    		$this->_helper->redirector('index','index', 'pm');
    		exit;
    	}
    	
		$form = $this->timer->getTimerForm(array(
            'action' => '/pm/timers/stop/',
            'method' => 'post',
        ));
       	if ($this->getRequest()->isPost()) 
		{
    		
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{			
				$time = new PM_Model_Times;
				$timer_data = array_merge($formData, $this->timer->decodeTimerData($this->prefs['timer_data']));
				$timer_data['hours'] = $timer_data['time_running']['hours'];
				$timer_data['creator'] = $this->identity;
				$timer_data['user_id'] = $this->identity;
				if($time->addTime($timer_data))
				{
					$this->timer->clearTimerData($this->identity);
					$this->_flashMessenger->addMessage('Timer Stopped!');
					$this->_helper->redirector('view-day','times', 'pm', array('date' => $timer_data['date']));
					exit;			
				}
			} 
		}
		
    	$this->view->timer_data = $this->timer->decodeTimerData($this->prefs['timer_data']);
    	$this->view->form = $form; 
    }
}