<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014 mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Api/src/Api/Model/Companies.php
 */

namespace Api\Model;

use PM\Model\Companies as PmCompanies;

/**
 * Api - Companies Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Model/Companies.php
 */
class Companies extends PmCompanies
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
	public $companiesOutputMap = array(
		'id' => 'id',
		'name' => 'name',
		'company_name' => 'company_name',
		'project_name' => 'project_name',
		'project_id' => 'project_id',
		'project_name' => 'project_name',
		'company_id' => 'company_id',
		'description' => 'description',
		'type' => 'type_id',
		'priority' => 'priority_id',
		'status' => 'status_id',
		'progress' => 'progress'
	);
	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Model\Tasks::getTasksByProjectId()
	 */
	public function getTasksByProjectId($id, array $where = null, array $not = null)
	{
		$tasks = parent::getTasksByProjectId($id, $where, $not);
		$total_results = $this->getTotalResults();

		$tasks = $this->cleanCollectionOutput($tasks, $this->taskOutputMap);
		if(count($tasks) >= 1)
		{
			$return = array(
				'data' => $tasks,
				'total_results' => (int)$total_results,
				'total' => count($tasks),
				'page' => (int)$this->getPage(),
				'limit' => $this->getLimit()
			);
			
			return $return;
		}
	}
}