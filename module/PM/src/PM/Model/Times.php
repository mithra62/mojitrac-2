<?php
/**
 * Times Model
 * @author Eric
 *
 */
class PM_Model_Times extends Model_Abstract
{
	/**
	 * The key to use for the cache items
	 * @var string
	 */
	public $cache_key = 'times';
			
	public function __construct()
	{
		parent::__construct();
		$this->db = new PM_Model_DbTable_Times;
	}
	
	/**
	 * Returns the Time Form
	 * @return object
	 */
	public function getTimeForm($options = array())
	{
        $form = new PM_Form_Time($options);
        return $form;		
	}	
	
	/**
	 * Returns the time for a given $id
	 * @param $name
	 * @return mixed
	 */
	public function getTimeById($id)
	{
		$sql = $this->db->select()
					  ->setIntegrityCheck(false)->from(array('t' => $this->db->getTableName()))
					  ->where('t.id = ?', $id);
		$sql->joinLeft(array('c' => 'companies'), 'c.id = t.company_id', array('name AS company_name'));
		$sql->joinLeft(array('p' => 'projects'), 'p.id = t.project_id', array('name AS project_name'));
		$sql->joinLeft(array('ta' => 'tasks'), 'ta.id = t.task_id', array('name AS task_name'));
		$sql->joinLeft(array('u' => 'users'), 'u.id = t.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
					  
		return $this->db->getTime($sql);
	}
	
	/**
	 * Returns an array of all the times
	 * @return mixed
	 */
	public function getAllTimes($date = FALSE, array $where = null, array $not = null)
	{
		if($date)
		{
			if(!is_array($where))
			{
				$where = array();
			}
			$where['i.date'] = $date;
		}
		
		return $this->getTimesWhere($where, $not);			
	}
	
	/**
	 * Returns all the times for a given $id
	 * @param int $id
	 * @return array
	 */
	public function getTimesByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['i.company_id'] = $id;
		return $this->getTimesWhere($where, $not);			
	}
	
	/**
	 * Returns all the times for a given project $id
	 * @param int $id
	 * @return array
	 */
	public function getTimesByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['i.project_id'] = $id;
		return $this->getTimesWhere($where, $not);			
	}
	
	/**
	 * Returns all the times for a given project $id
	 * @param int $id
	 * @return array
	 */
	public function getUserTimesByMonth($user, $month)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['i.project_id'] = $id;
		return $this->getTimesWhere($where, $not);			
	}	
	
	/**
	 * Returns all the times for a given task $id
	 * @param int $id
	 * @param array $where
	 * @param array $not
	 * @return array
	 */
	public function getTimesByTaskId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['i.task_id'] = $id;
		return $this->getTimesWhere($where, $not);	
	}
	
	/**
	 * Returns the time entries for the user_id
	 * @param int $id
	 * @param array $where
	 * @param array $not
	 * @return array
	 */
	public function getTimesByUserId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['i.user_id'] = $id;
		return $this->getTimesWhere($where, $not);	
	}
	
	public function getTimeByHarvestId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['i.harvest_id'] = $id;
		return $this->getTimesWhere($where, $not);		
	}
	
	/**
	 * Creates the SQL and performs the query to get times
	 * @param array $where
	 * @param array $not
	 * @param array $orwhere
	 * @param array $ornot
	 * @return array
	 */
	private function getTimesWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('i'=>$this->db->getTableName()));
		
		if(is_array($where))
		{
			foreach($where AS $key => $value)
			{
				$sql = $sql->where("$key = ? ", $value);
			}
		}
		
		if(is_array($not))
		{
			foreach($not AS $key => $value)
			{
				$sql = $sql->where("$key != ? ", $value);
			}
		}
		
		if(is_array($orwhere))
		{
			foreach($orwhere AS $key => $value)
			{
				$sql = $sql->orwhere("$key = ? ", $value);
			}
		}
		
		if(is_array($ornot))
		{
			foreach($ornot AS $key => $value)
			{
				$sql = $sql->orwhere("$key != ? ", $value);
			}
		}		
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = i.project_id', array('name AS project_name'));
		$sql = $sql->joinLeft(array('t' => 'tasks'), 't.id = i.task_id', array('name AS task_name'));
		$sql = $sql->joinLeft(array('c' => 'companies'), 'c.id = i.company_id', array('name AS company_name'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = i.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
		
		return $this->db->getTimes($sql);
	}	
	
	/**
	 * Returns the sum of all the times broken up by status
	 * @param int $id
	 * @return array
	 */
	public function getTotalTimesByCompanyId($id)
	{
		return $this->getTotalTimesWhere($id, FALSE, FALSE, FALSE);				
	}
	
	/**
	 * Returns the sum of all the times broken up by status
	 * @param int $id
	 * @return array
	 */
	public function getTotalTimesByProjectId($id)
	{
		return $this->getTotalTimesWhere(FALSE, FALSE, $id, FALSE);			
	}

	/**
	 * Returns the sum of all the times broken up by status
	 * @param int $id
	 * @return array
	 */
	public function getTotalTimesByTaskId($id)
	{
		return $this->getTotalTimesWhere(FALSE, FALSE, FALSE, $id);
	}
	
	public function getTotalTimesByUserId($id)
	{
		return $this->getTotalTimesWhere(FALSE, $id, FALSE, FALSE);
	}
	
	/**
	 * Returns the sum of all the times broken up by status
	 * @param int $id
	 * @return array
	 */
	public function getTotalTimesWhere($company_id = FALSE, $user_id = FALSE, $project_id = FALSE, $task_id = FALSE)
	{		
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('i'=>$this->db->getTableName()), array(new Zend_Db_Expr('SUM(hours) AS hours')));
		if($company_id)
		{
			$sql->where('i.company_id = ?', $company_id);
		}
		
		if($user_id)
		{
			$sql->where('i.user_id = ?', $user_id);
		}

		if($project_id)
		{
			$sql->where('i.project_id = ?', $project_id);
		}

		if($task_id)
		{
			$sql->where('i.task_id = ?', $task_id);
		}		
		$total = $this->db->getTime($sql);

		
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('i'=>$this->db->getTableName()), array(new Zend_Db_Expr('SUM(hours) AS hours')));
		$sql = $sql->where('bill_status = ?', 'sent')->where('billable = ?', 1);
		if($company_id)
		{
			$sql->where('i.company_id = ?', $company_id);
		}
		
		if($user_id)
		{
			$sql->where('i.user_id = ?', $user_id);
		}

		if($project_id)
		{
			$sql->where('i.project_id = ?', $project_id);
		}

		if($task_id)
		{
			$sql->where('i.task_id = ?', $task_id);
		}		
		$sent = $this->db->getTime($sql);
		
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('i'=>$this->db->getTableName()), array(new Zend_Db_Expr('SUM(hours) AS hours')));
		$sql = $sql->where('bill_status = ?', '')->where('billable = ?', 1);
		if($company_id)
		{
			$sql->where('i.company_id = ?', $company_id);
		}
		
		if($user_id)
		{
			$sql->where('i.user_id = ?', $user_id);
		}

		if($project_id)
		{
			$sql->where('i.project_id = ?', $project_id);
		}

		if($task_id)
		{
			$sql->where('i.task_id = ?', $task_id);
		}			
		$unsent = $this->db->getTime($sql);
		
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('i'=>$this->db->getTableName()), array(new Zend_Db_Expr('SUM(hours) AS hours')));
		$sql = $sql->where('bill_status = ?', 'paid')->where('billable = ?', 1);
		if($company_id)
		{
			$sql->where('i.company_id = ?', $company_id);
		}
		
		if($user_id)
		{
			$sql->where('i.user_id = ?', $user_id);
		}

		if($project_id)
		{
			$sql->where('i.project_id = ?', $project_id);
		}

		if($task_id)
		{
			$sql->where('i.task_id = ?', $task_id);
		}			
		$paid = $this->db->getTime($sql);

		return array('total' => $total['hours'], 'sent' => $sent['hours'], 'unsent' => $unsent['hours'], 'paid' => $paid['hours']);				
	}	
	
	public function getCalendarItems($month = FALSE, $year = FALSE, $user_id = FALSE)
	{
		//SELECT SUM(hours),creator,date FROM `times` GROUP BY date,creator
		$sql = $this->db->select()->setIntegrityCheck(false);
		$sql = $sql->from(array('i'=>$this->db->getTableName()),
						  		array(new Zend_Db_Expr('SUM(hours) AS total'), 'date', 'creator'));

		if($month)
		{
			$sql = $sql->where(new Zend_Db_Expr('DATE_FORMAT(i.date, "%M")').' = ? ', $month);
		}
		if($year)
		{
			$sql = $sql->where(new Zend_Db_Expr('DATE_FORMAT(i.date, "%Y")').'= ? ', $year);
		}
		if($user_id)
		{
			$sql = $sql->where('creator = ? ', $user_id);
		}				
		
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = i.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));				   
		
		$sql = $sql->group('date')
				   ->group('creator');
		return $this->_translateCalendarItems($this->db->getTimes($sql), 'date', 'Hours', 'Hour', 'Worked', 'creator');
	}
	
	private function _translateCalendarItems($arr, $master_key, $plural, $singular, $tail, $url_view)
	{
		$_arr = array();
		foreach($arr AS $key)
		{
			$_arr[$key[$master_key]][] = array(
											'string' => $key['creator_first_name'].' '.$key['creator_last_name'].' ('.number_format($key['total'], 2).')',
											'href' => '/pm/times/view-day/date/'.$key[$master_key].'/user/'.$key[$url_view],
											'rel' => ''
			);
		}
		
		return $_arr;		
	}	

	/**
	 * Inserts a Time and updates the counts
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addTime($data)
	{
		//check if we need to convert the time format to decimal
		$pos = strrpos($data['hours'], ":");
		if ($pos !== false)
		{ 
			$data['hours'] = $this->time_to_decimal($data['hours']);
		}	

		//update the date to ensure we're dealing with the right format
		
		$sql = $this->db->getSQL($data);
		$sql['creator'] = $data['creator'];

		$time_id = $this->db->addTime($sql);
		
		if(is_numeric($data['project_id']))
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$project->updateProjectTime($data['project_id'], $data['hours']);
		}
		
		if(is_numeric($data['task_id']))
		{
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$task->updateTaskTime($data['task_id'], $data['hours']);
		}
		
		return $time_id;
	}
	
	/**
	 * Updates a time entry
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateTime($data, $id)
	{
		$sql = $this->db->getSQL($data);
		return $this->db->update($sql, "id = '$id'");
	}
	
	public function updateBillStats($id, $status, $billable = '1')
	{
		$sql = array('bill_status' => $status, 'billable' => $billable);
		return $this->db->update($sql, "id = '$id'");
	}
	
	/**
	 * Handles everything for removing a time reference.
	 * @param $id
	 * @return bool
	 */
	public function removeTime($id, array $data = array())
	{
		if(isset($data['project_id']) && is_numeric($data['project_id']))
		{
			$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$project->updateProjectTime($data['project_id'], "-".$data['hours']);
		}
		
		if(isset($data['task_id']) && is_numeric($data['task_id']))
		{
			$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$task->updateTaskTime($data['task_id'], "-".$data['hours']);
		}	
		return $this->db->delete(array("id = '$id'"));
	}
	
	/**
	 * Convert decimal time into time in the format hh:mm:ss
	 * @param integer The time as a decimal value.
	 * @return string $time The converted time value.
	 */
	function decimal_to_time($decimal) 
	{
		echo $decimal;
	    $hours = floor($decimal);
	    $minutes = round(($decimal % 24));
	    $seconds = $decimal - (int)$decimal;
	    $seconds = round($seconds * 3600);
	    return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT);
	}
	
	/**
	 * Convert time into decimal time.
	 * @param string $time The time to convert
	 * @return integer The time as a decimal value.
	 */
	function time_to_decimal($time) 
	{
	    $timeArr = explode(':', $time);
	    if(!isset($timeArr['2']))
	    {
	    	$timeArr['2'] = 00;
	    }
	    
	    $decTime = (($timeArr[0]*60) + ($timeArr[1]) + ($timeArr[2]/3600))/60;
	    return round($decTime, 2);
	}	
}