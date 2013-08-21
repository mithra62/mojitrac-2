<?php
class PM_Model_Import_Harvest extends PM_Model_Import
{
	public $api;
	
	public $user;
	
	public $pass;
	
	public $account;
	
	public function __construct()
	{
		$this->api = FALSE;
	}
	
    public function getForm()
    {
    	return new PM_Form_Import_Harvest(array(
            'action' => '/pm/import/harvest',
            'method' => 'post',
        ));
    }

    public function importAll($user, $pass, $account, $ssl = FALSE)
    {
		$this->api = $this->setApi($user, $pass, $account);
		$this->importUsers();
		sleep(15);
		$this->importClients();
		sleep(15);
		$this->importProjects();
		sleep(15);
		$this->importTimes();
		return TRUE;
    }
    
    public function importTimes()
    {	
    	$user = new PM_Model_Users(new PM_Model_DbTable_Users);
    	$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
    	$time = new PM_Model_Times;
    	
    	$users = $user->getHarvestUsers();
    	$past = date('Ymd', mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")-1));
    	$range = new Harvest_Range( $past, date('Ymd') );
    	foreach($users AS $member)
    	{
    		$times = $this->api->getUserEntries($member['harvest_id'], $range);
    		foreach($times AS $entry)
    		{
    			
    			if($time->getTimeByHarvestId($entry['harvest_time_id']))
    			{	
    				continue;	
    			}
    			
	    		$proj_data = $project->getProjectByHarvestId($entry['harvest_project_id']);
	    		if(!$proj_data)
	    		{
	    			continue;
	    		}
	    		
	    		$entry['project_id'] = $proj_data['id'];
	
	    		$entry['task_id'] = '';
	    		$entry['description'] = $entry['notes'];
	    		if($entry['description'] == '')
	    		{
	    			//$entry['description'] = $
	    		}
	    		
	    		$entry['company_id'] = $proj_data['company_id'];
	    		$entry['user_id'] = $member['id'];
	    		$entry['creator'] = $member['id'];
	    		if($id = $time->addTime($entry))
	    		{
	    			$bill_status = '';
	    			if($entry['is_billed'] == '0')
	    			{
	    				$bill_status = 'sent';
	    			}
	    			
	    			if($entry['is_closed'] != 'false')
	    			{
	    				$bill_status = 'paid';
	    			}
	    			$sql = array('bill_status' => $bill_status, 'harvest_id' => $entry['harvest_time_id']);
	    			$time->db->updateTime($sql, $id);
	    		} 
    		} 
    	}
    }
    
    public function importUsers()
    {
    	$user = new PM_Model_Users(new PM_Model_DbTable_Users);
    	$users = $this->api->getUsers();
    	foreach($users AS $member)
    	{   			
    		if(!$user->getUserByEmail($member['email']))
    		{
    			$member['user_roles'][] = 2;
    			if($id = @$user->addUser($member))
    			{
    				$sql = array('harvest_id' => $member['harvest_user_id']);
    				$user->db->updateUser($sql, $id);
    			}   			
    		}   		
    	}
    	
    	return TRUE;
    }
    
    public function importProjects()
    {
        $proj = new PM_Model_Projects(new PM_Model_DbTable_Projects);
        $companies = new PM_Model_Companies(new PM_Model_DbTable_Companies);
        $ta = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
        $user = new PM_Model_Users(new PM_Model_DbTable_Users);
        
		$projects = $this->api->getProjects();
		$tasks = $this->api->getTasks();
        foreach($projects AS $harvest_company_id => $projects)
    	{  	

    		$company_id = $companies->getCompanyIdByHarvestId($harvest_company_id);
    		if(!$company_id)
    		{
    			continue;
    		}
    		
    		foreach($projects AS $project)
    		{
    			
    			if($proj->getProjectByHarvestId($project['harvest_project_id']))
    			{
    				continue;
    			}
    			
    			$sql = array('name' => $project['name']);
	    		$sql['company_id'] = $company_id;
	    		$sql['last_modified'] = new Zend_Db_Expr('NOW()');
	    		$sql['description'] = $project['description'];
	    		$sql['harvest_id'] = $project['harvest_project_id'];
	    		$sql['priority'] = 3;
	    		$sql['status'] = $project['status'];
	    		$sql['start_date'] = $project['start_date'];
	    		if($project_id = @$proj->db->addProject($sql))
	    		{
	    			if((int)$project['assigned_user_count'] >= 1)
	    			{
	    				$project['assigned_users'] = $this->api->getProjectUserAssignments($project['harvest_project_id']);
	    				foreach($project['assigned_users'] AS $assigned_users)
	    				{
	    					$user_data = $user->getUserByHarvestId($assigned_users['harvest_user_id']);
	    					$proj->addProjectTeamMember($user_data['id'], $project_id);
	    				}
	    			}
	    			
	    			if((int)$project['assigned_tasks_count'] >= 1)
	    			{
	    				$project['assigned_tasks'] = $this->api->getProjectTaskAssignments($project['harvest_project_id']);
	    				foreach($project['assigned_tasks'] AS $task)
	    				{    					
	    					if(array_key_exists($task['task_id'], $tasks))
	    					{
	    						//add the task	    						
	    						$sql = array();
	    						$sql['company_id'] = $company_id;
	    						$sql['project_id'] = $project_id;
	    						$sql['name'] = $tasks[$task['task_id']]['name'];
	    						$sql['description'] = $tasks[$task['task_id']]['description'];
	    						$sql['assigned_to'] = 0;
	    						$sql['progress'] = 0;
	    						$sql['access'] = 0;
	    						$sql['type'] = 0;
	    						$sql['priority'] = 3;
	    						$sql['status'] = 3;
	    						$sql['creator'] = Zend_Auth::getInstance()->getIdentity();
	    						$ta->addTask($sql);
	    					}
	    				}
	    			}
	    		}	    	
    		}
    	} 	
    }
    
    public function importClients()
    {
    	$companies = new PM_Model_Companies(new PM_Model_DbTable_Companies);
    	$clients = $this->api->getClients();
    	foreach($clients AS $client)
    	{  	
    		$company = $companies->getCompanyIdByName($client['name']);
    		if($company)
    		{
    			continue;
    		}
    		
    		$sql = array();
    		$sql['type'] = 1;
    		$sql['last_modified'] = new Zend_Db_Expr('NOW()');
    		$sql['name'] = $client['name'];
    		$sql['description'] = $client['description'];
    		$sql['harvest_id'] = $client['harvest_client_id'];
    		@$companies->db->addCompany($sql);
    	}

    }
    
    public function setApi($user, $pass, $account)
    {
    	if(!$this->api)
    	{
    		return new PM_Model_Rest_Harvest($user, $pass, $account, TRUE);
    	}
    	
    	return $this->api;
    }
    
    public function importTimesFile($file_path)
    {
    	if(!file_exists($file_path))
    	{
    		echo "Can't find file!";
    		exit;
    	}
    	
    	$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
    	$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
    	$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
    	$time = new PM_Model_Times;
    	$user = new PM_Model_Users(new PM_Model_DbTable_Users);
    	$file = file($file_path);
    	
    	
    	$count = 0;
    	foreach($file AS $num => $line)
    	{
    		$parts = explode("\t",$line);
    		$company_id = FALSE;
    		$project_id = FALSE;
    		$task_id = FALSE;
    		if(isset($parts['1']))
    		{
    			$company_data = $company->getCompanyIdByName($parts['1']);
    			if(isset($company_data['id']))
    			{
    				$company_id = $company_data['id'];
    				$project_data = $project->getByCompanyIdProjectName($parts['2'], $company_id);
    				if(isset($project_data['id']) && $project_data['id'] != '')
    				{
    					$project_id = $project_data['id'];
    					$task_data = $task->getByProjectIdTaskName(str_replace('"', '', $parts['4']), $project_id);
    					if(isset($task_data['id']))
    					{
    						$task_id = $task_data['id'];
    					}
    				}
    				
    				if(isset($parts['7']) && $parts['7'] != '' && isset($parts['8']) && $parts['8'] != '')
    				{
    					$user_data = $user->getUserByFirstLastName($parts['7'], $parts['8']);
    				}
    				if(!isset($user_data['id']))
    				{
    					continue;
    				}
    				
    				$entry = array();
    				$user_id = $user_data['id'];
		    		$entry['project_id'] = $project_id;
		    		$entry['company_id'] = $company_id;
		    		$entry['task_id'] = $task_id;
		    		$entry['user_id'] = $user_id;
		    		$entry['creator'] = $user_id;
		    		$entry['description'] = '';
		    		if(isset($parts['5']) && $parts['5'] != '' && $parts['5'] != '""')
		    		{
		    			$entry['description'] = $parts['5'];
		    		}
		    		
		    		if(isset($parts['0']))
		    		{
		    			$entry['date'] = date('Y-m-d', strtotime($parts['0']));
		    			if(!$entry['date'])
		    			{
		    				continue;
		    			}
		    		}
    				else
		    		{
		    			continue;
		    		}
		    				    		
    				if(isset($parts['6']))
		    		{
		    			$entry['hours'] = $parts['6'];
		    		}
		    		else
		    		{
		    			continue;
		    		}
		    		
		    		if(isset($parts['9']))
		    		{
		    			$entry['billable'] = ($parts['9'] == 'billable' ? '1' : '0');
		    		}
		    		 
		    		//echo $num.' ';
//		    		/print_r($entry);
//		    		/continue;
		    		if($id = $time->addTime($entry))
		    		{

		    		}     				
       			}
    			else
    			{
    				continue;
    			}
    		}
    	}    	
    }
}