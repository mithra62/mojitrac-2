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
		'phone1' => 'phone1',
		'phone2' => 'phone2',
		'address1' => 'address1',
		'address2' => 'address2',
		'city' => 'city',
		'state' => 'state',
		'zip' => 'zip',
		'primary_url' => 'primary_url',
		'description' => 'description',
		'type' => 'type'
	);
	
	/**
	 * (non-PHPdoc)
	 * @see \PM\Model\Companies::getAllCompanies()
	 */
	public function getAllCompanies($view_type = FALSE)
	{
		$companies = parent::getAllCompanies($view_type);
		$total_results = $this->getTotalResults();

		if(count($companies) >= 1)
		{
			$return = array(
				'data' => $companies,
				'total_results' => (int)$total_results,
				'total' => count($companies),
				'page' => (int)$this->getPage(),
				'limit' => $this->getLimit()
			);
			
			return $return;
		}
	}
}