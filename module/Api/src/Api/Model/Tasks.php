<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Model/Tasks.php
 */

namespace Api\Model;

use PM\Model\Tasks as PmTasks;

/**
 * Api - Tasks Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Model/Tasks.php
 */
class Tasks extends PmTasks
{
	/**
	 * Determines wheher we should filter results based on REST output
	 * @var bool
	 */
	private $filter = TRUE;
	
	/**
	 * The REST output for the tasks db table 
	 * @var array
	 */
	public $taskOutputMap = array(
		'id' => 'task_id',
		'name' => 'task_name',
		'company_name' => 'company_name',
		'project_name' => 'project_name',
		'project_id' => 'project_id',
		'company_id' => 'company_id',
		'description' => 'description',
		'type' => 'type_id',
		'priority' => 'priority_id',
		'status' => 'status_id',
		'progress' => 'progress'
	);
	
	/**
	 * The REST output for the assignments db table
	 * @var array
	 */
	public $taskAssignmentMap = array(
		'id' => 'assignment_id',
		'assigned_by' => 'assigned_by_member_id',
		'assigned_to' => 'assigned_to_member_id',
		'comments' => 'comments',
		'assigned_date' => 'created_date',
		'to_first_name' => 'assigned_to_first_name',
		'to_last_name' => 'assigned_to_last_name',
		'by_first_name' => 'assigned_by_first_name',
		'by_last_name' => 'assigned_by_last_name',
	);
	
	/**
	 * Cleans up single resources to remove unwanted fields/keys
	 * @param array $data
	 * @param array $map
	 * @return multitype:array
	 */
	public function cleanResourceOutput(array $data, array $map)
	{
		//first, tidy things up so we're not just dumping db results
		$return = array();
		foreach($data AS $key => $value)
		{
			foreach($map AS $k => $v)
			{
				if( $key == $k )
				{
					$return[$v] = $value;
				}			
			}
		}
		
		return $return;
	}
	
	/**
	 * Cleans up collections to remove unwanted fields/keys
	 * @param array $data
	 * @param array $map
	 * @return multitype:array
	 */
	public function cleanCollectionOutput(array $data, array $map)
	{
		//first, tidy things up so we're not just dumping db results
		$return = array();
		foreach($data AS $key => $value)
		{
			$return[$key] = array();
			foreach($value AS $k => $v)
			{
				foreach($map AS $map_key => $map_value)
				{
					if( $k == $map_key )
					{
						$return[$key][$map_value] = $v;
					}
				}
			}
		}
		
		return $return;
	}	
	
	/**
	 * Sets `filter`
	 * @param string $filter
	 */
	public function setFilter($filter = FALSE)
	{
		$this->filter = $filter;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Model\Tasks::getTaskById()
	 */
	public function getTaskById($id, array $what = null)
	{
		$tasks = parent::getTaskById($id, $what);
		return ($this->filter ? $this->cleanResourceOutput($tasks, $this->taskOutputMap) : $tasks);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Model\Tasks::getTaskAssignments()
	 */
	public function getTaskAssignments($id)
	{
		$assignments = parent::getTaskAssignments($id);
		return $this->cleanCollectionOutput($assignments, $this->taskAssignmentMap);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Model\Tasks::getTasksByProjectId()
	 */
	public function getTasksByProjectId($id, array $where = null, array $not = null)
	{
		$tasks = parent::getTasksByProjectId($id, $where, $not);
		return $this->cleanCollectionOutput($tasks, $this->taskOutputMap);
	}
}