<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/TimersController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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

	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
		parent::preDispatch();
        parent::check_permission('track_time');
        $this->view->headTitle('Timers', 'PREPEND');
		$this->view->layout_style = 'single';
		$this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'times';
        $this->view->active_nav = 'time';
		$this->view->active_sub = 'None';
		$this->view->title = FALSE;

		$this->timer = new PM_Model_Timers;
    	$this->company_id = $this->_getParam("company",FALSE);
    	$this->task_id = $this->_getParam("task",FALSE);
    	$this->project_id = $this->_getParam("project",FALSE);
    	
    	$where = array();
        if($this->company_id)
    	{
	        parent::check_permission('view_companies');	    		
			$this->company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$this->company_data = $this->company->getCompanyById($this->company_id);
			if(!$this->company_data) 
			{
				$this->_helper->redirector('index','companies');
				exit;				
			}
			$this->view->company = $this->company_data;
    	}
    	
    	if($this->project_id) 
		{
			$this->project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$this->project_data = $this->project->getProjectById($this->project_id);
			if(!$this->project_data)
			{
				$this->_helper->redirector('index','projects');
				exit;				
			}
			
			if(!$this->project->isUserOnProjectTeam($this->identity, $this->project_id) && !$this->perm->check($this->identity, 'manage_time'))
			{		
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}
						
			$this->view->project = $this->project_data;
		}    

    	if($this->task_id) 
		{
			$this->task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$this->project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$this->task_data = $this->task->getTaskById($this->task_id);
			if(!$this->task_data)
			{
				$this->_helper->redirector('index','tasks');
				exit;				
			}
			
			if(!$this->project->isUserOnProjectTeam($this->identity, $this->task_data['project_id']) && !$this->perm->check($this->identity, 'manage_time'))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}
						
			$this->view->task = $this->task_data;
		}
	}
    
    public function indexAction()
    {
    	$date = $this->_request->getParam('date', date('F Y'));
    }
    
    public function viewAction()
    {	

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
        	if($this->task_id)
        	{
        		$timer = $this->timer->startTaskTimer($this->identity, $this->task_id);
        	} 
        	elseif($this->project_id)
        	{
        		$timer = $this->timer->startProjectTimer($this->identity, $this->project_id);
        	}
        	elseif($this->company_id)
        	{
        		$timer = $this->timer->startCompanyTimer($this->identity, $this->company_id);
        	}
        	
        	
        	if($timer)
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