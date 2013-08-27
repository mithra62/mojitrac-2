<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Companies.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

 /**
 * PM - Companies Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Companies.php
 */
class Companies extends AbstractModel
{
	protected $inputFilter;
	
	/**
	 * The Companies Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
			'name' => $data['name'],
			'phone1' => $data['phone1'],
			'phone2' => $data['phone2'],
			'fax' => $data['fax'],
			'address1' => $data['address1'],
			'address2' => $data['address2'],
			'city' => $data['city'],
			'state' => $data['state'],
			'zip' => $data['zip'],
			'primary_url' => $data['primary_url'],
			'description' => $data['description'],
			'type' => $data['type'],
			'custom' => $data['custom'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getCompanyIdByName($name)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $this->db->getCompany($sql);
	}
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getCompanyIdByHarvestId($harvest_id)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('harvest_id = ?', $harvest_id);
					  
		$company = $this->db->getCompany($sql);
		if($company)
		{
			return $company['id'];
		}
	}	
	
	public function getCompanyById($id, $what = null)
	{
		$sql = $this->db->select();
		if(is_array($what))
		{
			$sql->from(array('c'=> 'companies'), $what);
		}
		else
		{
			$sql->from(array('c'=> 'companies'));
		}
				
		$sql->where(array('id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Returns an array of all unique artist names
	 * @return mixed
	 */
	public function getAllCompanyNames($type = FALSE, $ids = FALSE)
	{
		$sql = $this->db->select()->from('companies', array('id','name'));
		if($type && is_array($type))
		{ 
			foreach($type AS $t)
			{
				$sql = $sql->orwhere('type = ?', $t);
			}
		}
		
		if($ids && is_array($ids))
		{ 
			$sql = $sql->where('id IN (?)', $ids);
		}
		
		$sql = $sql->order('name ASC');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllCompanies($view_type = FALSE)
	{
		$sql = $this->db->select()->from('companies');
		if(is_numeric($view_type))
		{
			$sql = $sql->where(array('type' => $view_type));
		}
		
		return $this->getRows($sql);		
	}
	
	/**
	 * Returns the total projects a company has
	 * @param int $id
	 * @return int
	 */
	public function getProjectCount($id, $status = FALSE)
	{
		$sql = $this->db->select()
					->from('projects', array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where(array('company_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return (!empty($data['count']) ? $data['count'] : '0');
		}
	}
	
	/**
	 * Returns the total tasks a company has
	 * @param int $id
	 * @param int $status
	 * @return int
	 */
	public function getTaskCount($id, $status = FALSE)
	{
		$sql = $this->db->select()
					->from('tasks', array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where(array('company_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return (!empty($data['count']) ? $data['count'] : '0');
		}		
	}
	
	/**
	 * Returns the total files a company has
	 * @param int $id
	 * @param int $status
	 * @return int
	 */
	public function getFileCount($id, $status = FALSE)
	{
		$sql = $this->db->select()
					->from('files', array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where(array('company_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return (!empty($data['count']) ? $data['count'] : '0');
		}		
	}
	
	/**
	 * Inserts or updates a Company
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addCompany($data)
	{
		$sql = $this->getSQL($data);
		$company_id = $this->insert('companies', $sql);
		return $company_id;
	}
	
	/**
	 * Updates a company
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateCompany($data, $id)
	{
		$sql = $this->getSQL($data);
		return $this->update('companies', $sql, array('id' => $id));
	}
	
	/**
	 * Updates the total project counts for a given $id
	 * @param int $id
	 * @param int $count
	 * @param string $col
	 * @return bool
	 */
	public function updateCompanyProjectCount($id, $count = 1, $col = 'active_projects')
	{
		$sql = array($col => new \Zend\Db\Sql\Expression($col.'='.$col.'+'.$count));
		return $this->update('companies', $sql, array('id' => $id));
	}	
	
	/**
	 * Handles everything for removing a company.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeCompany($id)
	{
		if($this->db->deleteCompany($id))
		{
			$projects = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$projects->removeProjectsByCompany($id);
			
			$tasks = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$tasks->removeTasksByCompany($id);
			
			$files = new PM_Model_Files(new PM_Model_DbTable_Files);
			$files->removeFilesByCompany($id);

			$notes = new PM_Model_Notes;
			$notes->removeNotesByCompany($id);

			$bookmarks = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
			$bookmarks->removeBookmarksByCompany($id);			
		}
		
		return TRUE;
	}
}