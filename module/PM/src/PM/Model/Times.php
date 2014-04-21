<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./module/PM/src/PM/Model/Times.php
 */

namespace PM\Model;

use Application\Model\AbstractModel;

 /**
 * PM - Times Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Times.php
 */
class Times extends AbstractModel
{
	/**
	 * The key to use for the cache items
	 * @var string
	 */
	public $cache_key = 'times';
			
	/**
	 * The Times Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Returns the time for a given $id
	 * @param $name
	 * @return mixed
	 */
	public function getTimeById($id)
	{
		$sql = $this->db->select()
					  ->from(array('t' => 'times'))
					  ->where(array('t.id' => $id));
		$sql->join(array('c' => 'companies'), 'c.id = t.company_id', array('company_name' => 'name'), 'left');
		$sql->join(array('p' => 'projects'), 'p.id = t.project_id', array('project_name' => 'name'), 'left');
		$sql->join(array('ta' => 'tasks'), 'ta.id = t.task_id', array('task_name' => 'name'), 'left');
		$sql->join(array('u' => 'users'), 'u.id = t.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
					  
		return $this->getRow($sql);
	}
	
	/**
	 * Returns an array of all the times
	 * @return mixed
	 */
	public function getAllTimes(array $where = null, array $not = null)
	{	
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
		$sql = $this->db->select()->from(array('i'=> 'times'));
		
		if(is_array($where))
		{
			foreach($where AS $key => $value)
			{
				$sql = $sql->where(array($key => $value));
			}
		}

		if(is_array($not))
		{
			foreach($not AS $key => $value)
			{
				$sql = $sql->where("$key != '$value'");
			}
		}
		
		if(is_array($orwhere))
		{
			foreach($orwhere AS $key => $value)
			{
				$sql = $sql->orwhere(array($key => $value), 'OR');
			}
		}
		
		if(is_array($ornot))
		{
			foreach($ornot AS $key => $value)
			{
				$sql = $sql->orwhere("$key != ? ", $value);
			}
		}		
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = i.project_id', array('project_name' => 'name'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = i.task_id', array('task_name' => 'name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = i.company_id', array('company_name' => 'name'), 'left');
		$sql = $sql->join(array('u' => 'users'), 'u.id = i.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		
		return $this->getRows($sql);
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
		$sql = $this->db->select()->from(array('i'=>'times'))->columns(array('hours' => new \Zend\Db\Sql\Expression('SUM(hours)')));
		if($company_id)
		{
			$sql->where(array('company_id' => $company_id));
		}
		
		if($user_id)
		{
			$sql->where(array('user_id' => $user_id));
		}

		if($project_id)
		{
			$sql->where(array('project_id' => $project_id));
		}

		if($task_id)
		{
			$sql->where(array('task_id' => $task_id));
		}
				
		$total = $this->getRow($sql);

		
		$sql = $this->db->select()->from(array('i'=>'times'))->columns(array('hours' => new \Zend\Db\Sql\Expression('SUM(hours)')));
		$sql = $sql->where(array('bill_status' => 'sent', 'billable' => 1));
		if($company_id)
		{
			$sql->where(array('company_id' => $company_id));
		}
		
		if($user_id)
		{
			$sql->where(array('user_id' => $user_id));
		}

		if($project_id)
		{
			$sql->where(array('project_id' => $project_id));
		}

		if($task_id)
		{
			$sql->where(array('task_id' => $task_id));
		}		
		$sent = $this->getRow($sql);
		
		$sql = $this->db->select()->from(array('i'=> 'times'))->columns( array('hours' => new \Zend\Db\Sql\Expression('SUM(hours)')));
		$sql = $sql->where(array('bill_status' => '', 'billable' => 1));
		if($company_id)
		{
			$sql->where(array('company_id' => $company_id));
		}
		
		if($user_id)
		{
			$sql->where(array('user_id' => $user_id));
		}

		if($project_id)
		{
			$sql->where(array('project_id' => $project_id));
		}

		if($task_id)
		{
			$sql->where(array('task_id' => $task_id));
		}			
		$unsent = $this->getRow($sql);
		
		$sql = $this->db->select()->from(array('i'=>'times'))->columns( array('hours' => new \Zend\Db\Sql\Expression('SUM(hours)')));
		$sql = $sql->where(array('bill_status' => 'paid', 'billable' => '1'));
		if($company_id)
		{
			$sql->where(array('company_id' => $company_id));
		}
		
		if($user_id)
		{
			$sql->where(array('user_id' => $user_id));
		}

		if($project_id)
		{
			$sql->where(array('project_id' => $project_id));
		}

		if($task_id)
		{
			$sql->where(array('task_id' => $task_id));
		}			
		$paid = $this->getRow($sql);
		return array('total' => $total['hours'], 'sent' => $sent['hours'], 'unsent' => $unsent['hours'], 'paid' => $paid['hours']);				
	}	
	
	public function getCalendarItems($month = FALSE, $year = FALSE, $user_id = FALSE)
	{
		$sql = $this->db->select();
		$sql = $sql->from(array('i'=>'times'))->columns(array(new \Zend\Db\Sql\Expression('SUM(hours) AS total'), 'date', 'creator'));

		if($month)
		{
			$sql = $sql->where(array('month' => $month));
		}
		
		if($year)
		{
			$sql = $sql->where(array('year' => $year));
		}
		
		if($user_id)
		{
			$sql = $sql->where('creator = ? ', $user_id);
		}				
		
		$sql = $sql->join(array('u' => 'users'), 'u.id = i.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');				   
		$sql = $sql->group('date')
				   ->group('creator');
		return $this->_translateCalendarItems($this->getRows($sql), 'date', 'Hours', 'Hour', 'Worked', 'creator');
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
	 * @param int $id
	 * @param array $data
	 * @param \PM\Model\Projects $project
	 * @param \PM\Model\Tasks $task
	 */
	public function removeTime($id, array $data = array(), \PM\Model\Projects $project, \PM\Model\Tasks $task)
	{
		if(isset($data['project_id']) && is_numeric($data['project_id']))
		{
			$project->updateProjectTime($data['project_id'], "-".$data['hours']);
		}
		
		if(isset($data['task_id']) && is_numeric($data['task_id']))
		{
			$task->updateTaskTime($data['task_id'], "-".$data['hours']);
		}	
		
		return $this->remove('times', array('id' => $id));
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