<?php
class PM_Model_Timers extends PM_Model_User_Data
{
	private $options = array('task_id' => 0, 'project_id' => 0, 'company_id' => 0, 'start_time' => FALSE);
	
	/**
	 * Used to determine cache uniqueness
	 * @var string
	 */
	public $cache_key = 'timers';
	
	public function __construct()
	{
		parent::__construct();	
	}
	
	/**
	 * Wrapper to handle the task time functionality
	 * @param int $identity
	 * @param int $task_id
	 */
	public function startTaskTimer($identity, $task_id)
	{
		return $this->startTimer($identity, array('task_id' => $task_id));
	}
	
	/**
	 * Wrapper to handle the project time functionality
	 * @param int $identity
	 * @param int $project_id
	 */
	public function startProjectTimer($identity, $project_id)
	{
		return $this->startTimer($identity, array('project_id' => $project_id));
	}
	
	/**
	 * Wrapper to handle the company time functionality
	 * @param int $identity
	 * @param int $company_id
	 */
	public function startCompanyTimer($identity, $company_id)
	{
		return $this->startTimer($identity, array('company_id' => $company_id));
	}	
	
	/**
	 * Handles the actual starting of a timer
	 * @param array $options
	 */
	public function startTimer($identity, array $options)
	{
		$options['start_time'] = mktime();
		return $this->updateUserDataEntry('timer_data', Zend_Json::encode($options), $identity);
	}
	
	/**
	 * Converts the timer string into an array
	 * @param string $str
	 * @return array
	 */
	public function decodeTimerData($str)
	{
		$data = Zend_Json::decode($str);
		if(isset($data['task_id']))
		{
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$task_data = $task->getTaskById($data['task_id'], array('t.name AS name'));
			$data = array_merge($data, $task_data);
		}
		elseif(isset($data['project_id']))
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$project_data = $project->getProjectById($data['project_id'], array('p.name AS name', 'p.company_id'));
			$data = array_merge($data, $project_data);			
		}
		elseif(isset($data['company_id']))
		{
			$company = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$company_data = $company->getCompanyById($data['company_id'], array('c.name AS name'));
			$data = array_merge($data, $company_data);			
		}		
		
		if(!isset($data['name']))
		{
			return FALSE;
		}
		
		$data['date'] = date('Y-m-d', $data['start_time']);
		$data['time_running'] = $this->makeTimeRunning($data['start_time']);
		return $data;
	}
	
	/**
	 * Removes the entry for the timer
	 * @param int $identity
	 */
	public function clearTimerData($identity)
	{
		return $this->updateUserDataEntry('timer_data', '', $identity);
	}
	
	/**
	 * Returns an array for how long the timer has been running
	 * @param array $start_time
	 */
	public function makeTimeRunning($start_time)
	{
		$return = array();
		$diff = time()-$start_time;
		$return['minutes'] = round($diff/60, 2);
		$return['hours'] = round($return['minutes']/60, 2);
		return $return;
	}
	
	/**
	 * Returns the format needed for the view object's JavaScript display
	 * @param string $str
	 */
	public function makeCountdownDate($str)
	{
		return date('F d Y H:i:s', $str);
	}
	
	/**
	 * Returns the timer form
	 * @param object $options
	 */
	public function getTimerForm($options)
	{
		return new PM_Form_Timer($options);
	}
}