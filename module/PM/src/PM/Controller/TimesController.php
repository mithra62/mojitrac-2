<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/TimesController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;

/**
* PM - Times Controller
*
* Routes the Times requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/TimesController.php
*/
class TimesController extends AbstractPmController
{
	/**
	 * Class onDispatch
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );  
		return $e;       
	}
    
    public function indexAction()
    {
    	$times = $this->getServiceLocator()->get('PM\Model\Times');
    	$month = $this->params()->fromRoute('month', date('m'));
    	$year = $this->params()->fromRoute('year', date('Y'));
    	
    	$view['month'] = $month;
    	$view['year'] = $year;

    	if($this->perm->check($this->identity, 'manage_time'))
    	{
    		$items = array();//$times->getCalendarItems($month, $year);
    		$view['calendar_data'] = $items;
    	}
    	else
    	{
    		$view['calendar_data'] = $times->getCalendarItems($month, $year, $this->identity);
    	}
    	
    	return $view;
    }
    
    public function removeAction()
    {
    	$time = new PM_Model_Times;
    	$id = $this->_request->getParam('id', FALSE);
		$confirm = $this->_getParam("confirm",FALSE);
		$fail = $this->_getParam("fail",FALSE);
		
    	if(!$id)
    	{
    		$this->_helper->redirector('index','times');
    		exit;
    	}
    	
    	
        $time_data = $time->getTimeById($id);
    	$this->view->time_data = $time_data;
    	if(!$this->view->time_data)
    	{
			$this->_helper->redirector('index','times');
			exit;
    	}

    	if($fail)
    	{
			$this->_helper->redirector('view','times', 'pm', array('id' => $id));
			exit;   		
    	}
    	
    	if($confirm)
    	{
    		if($time->removeTime($id, $time_data))
    		{	
				$this->_flashMessenger->addMessage('Time Removed');
				$this->_helper->redirector('view-day','times', 'pm', array('date' => $time_data['date']));
				exit;
    		}
    	}
   	
    }
    
    public function viewDayAction()
    {
    	
        $date = $this->_request->getParam('date', FALSE);
    	if(!$date)
    	{
	    	$date = date('Y-m-d');
    	}
    	    	
	    $times = new PM_Model_Times;
		$form = $times->getTimeForm(array(
            'action' => '/pm/times/view-day',
            'method' => 'post',
        ));
		
		 if ($this->getRequest()->isPost()) 
		 {
    		
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				$formData['creator'] = $this->identity;
				$formData['user_id'] = $this->identity;
				
				if($id = $times->addTime($formData))
				{
					$date = $this->_request->getParam('date', FALSE);
			    	$this->_flashMessenger->addMessage('Time Added!');
					$this->_helper->redirector('view-day','times', 'pm', array('date' => $formData['date']));
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
			}
		}    	
    	
		$this->view->date = $date;
		$view = $this->_getParam("view",FALSE);
		$this->view->active_sub = $view;
		
		if($this->perm->check($this->identity, 'manage_time'))
    	{		
	    	$this->view->times = $times->getAllTimes($date); 
    	}
    	else
    	{
    		$where = array('i.creator' => $this->identity);
    		$this->view->times = $times->getAllTimes($date, $where); 
    	}

	    $this->view->form = $form;    
    }
    
    public function viewAction()
    {
    	$times = new PM_Model_Times;
    	$company_id = $this->_getParam("company",FALSE);
    	$task_id = $this->_getParam("task",FALSE);
    	$project_id = $this->_getParam("project",FALSE);
    	$user_id = $this->_getParam("user",FALSE);
    	
    	$export = $this->_getParam("export",FALSE);
    	$bill_status = $this->_getParam("status",FALSE);
    	
    	//we're downloading the timesheets so kill layout
    	if($export)
    	{
    		$this->view->layout()->disableLayout();
    	}
    	
		$this->view->sub_menu = 'time_status';
		$this->view->active_sub = $bill_status;
			    	
    	$where = array();
    	if($bill_status)
		{
			if(!$this->perm->check($this->identity, 'manage_time'))
			{
	        	$this->_helper->redirector('view', 'times', 'pm');
	        	exit;				
			}
			
			$status_types = array('sent', 'unsent', 'paid', '');
			if(in_array($bill_status, $status_types))
			{
				$this->view->bill_status = $bill_status;
				if($bill_status == 'unsent')
				{
					$bill_status = '';
				}
				
				$where = array('bill_status' => $bill_status, 'billable' => '1');
			}
			elseif($bill_status == 'unbillable')
			{
				$where = array('billable' => '0');
			}
		}
		
        if($company_id)
    	{
			if(!$this->perm->check($this->identity, 'view_companies'))
	        {
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;
	        }	    		
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data) 
			{
				$this->_helper->redirector('index','companies');
				exit;				
			}
			$this->view->company = $company_data;
			$this->view->times  = $times->getTimesByCompanyId($company_id, $where);
			$this->view->type = 'company';
			$this->view->id = $company_id;
    	}
    	elseif($project_id) 
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$project_data = $project->getProjectById($project_id);
			if(!$project_data)
			{
				$this->_helper->redirector('index','projects');
				exit;				
			}
			
			if(!$project->isUserOnProjectTeam($this->identity, $project_id) && !$this->perm->check($this->identity, 'manage_time'))
			{		
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}
						
			$this->view->project = $project_data;
			$this->view->times = $times->getTimesByProjectId($project_id, $where);
			$this->view->type = 'project';
			$this->view->id = $project_id;
		}
    	elseif($task_id) 
		{
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$task_data = $task->getTaskById($task_id);
			if(!$task_data)
			{
				$this->_helper->redirector('index','tasks');
				exit;				
			}
			
			if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_time'))
			{
	        	$this->_helper->redirector('index', 'index', 'pm');
	        	exit;				
			}
						
			$this->view->task = $task_data;
			$this->view->times = $times->getTimesByTaskId($task_id, $where);
			$this->view->type = 'task';
			$this->view->id = $task_id;
		}
		elseif($user_id)
		{
			$user = new PM_Model_Users(new PM_Model_DbTable_Users);
			$user_data = $user->getUserById($user_id);
			if(!$user_data)
			{
				$this->_helper->redirector('index','users');
				exit;				
			}
						
			$this->view->user = $user_data;
			$this->view->times = $times->getTimesByUserId($user_id, $where);
			$this->view->type = 'user';
			$this->view->id = $user_id;			
		}
		else
		{
			$this->_helper->redirector('index','tasks');
			exit;			
		}

		if($export)
		{
			LambLib_Controller_Action_Helper_Utilities::downloadArray($this->view->times, TRUE, $this->view->bill_status.'_times.xls');
		}
    }
    
    public function updateAction()
    {
		 if (!$this->getRequest()->isPost()) 
		 {
	        $this->_helper->redirector('index', 'index', 'pm');
	        exit;
		 }
		 
    	if(!$this->perm->check($this->identity, 'manage_time'))
		{
	        $this->_helper->redirector('index', 'index', 'pm');
	        exit;				
		}

		$formData = $this->getRequest()->getPost();	
		if(!array_key_exists('update_type', $formData))
		{
	        $this->_helper->redirector('index', 'index', 'pm');
	        exit;			
		}

		$success = FALSE;
		$status = '';
		$time = new PM_Model_Times;
		if(array_key_exists('mark_paid', $formData))
		{
			$status = 'paid';
		}
		elseif(array_key_exists('mark_unpaid', $formData))
		{
			$status = '';
		}
    	elseif(array_key_exists('mark_pending', $formData))
		{
			$status = 'sent';
		}
		
		foreach($formData['time_update'] AS $key => $value)
		{
			if($time->updateBillStats($key, $status))
			{
				$sucess = TRUE;
			}
		}
		
		if($sucess)
		{
			$this->_flashMessenger->addMessage('Billing Status Updated!');
			if(array_key_exists('company', $formData))
			{
				$this->_helper->redirector('view','times', 'pm', array('company' => $formData['company'], 'status' => $formData['status']));
				exit;				
			}
			
			if(array_key_exists('project', $formData))
			{
				$this->_helper->redirector('view','times', 'pm', array('project' => $formData['project'], 'status' => $formData['status']));
				exit;				
			}			
			
			$this->_helper->redirector('view-day','times', 'pm', array('date' => $formData['date']));
			exit;	
		}
		
		$this->_helper->redirector('index','index', 'pm');
		exit;
    }
    
    public function searchAction()
    {
		$this->view->title = "Search Times";
		$this->view->headTitle("Search Times", 'PREPEND');    	
    }
}