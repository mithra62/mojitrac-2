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
				'name'     => 'email',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'EmailAddress',
					),
					array(
						'name' => 'Db\RecordExists',
						'options' => array(
							'table' => 'users',
							'field' => 'email',
							'adapter' => $this->authAdapter
						)
					),
				),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}	
	
	/**
	 * Returns the Artist Form
	 * @return object
	 */
	public function getCompanyForm($options = array(), $hidden = array())
	{
        return new PM_Form_Company($options, $hidden);		
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
		$proj = new PM_Model_DbTable_Projects;
		$sql = $proj->select()
					->from($proj->getTableName(), array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where('company_id = ?', $id);
		$data = $proj->getProject($sql);
		if(is_array($data))
		{
			return $data['count'];
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
		$task = new PM_Model_DbTable_Tasks;
		$sql = $task->select()
					->from($task->getTableName(), array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where('company_id = ?', $id);
		$data = $task->getTask($sql);
		if(is_array($data))
		{
			return $data['count'];
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
		$file = new PM_Model_DbTable_Files;
		$sql = $file->select()
					->from($file->getTableName(), array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where('company_id = ?', $id);
		$data = $file->getFile($sql);
		if(is_array($data))
		{
			return $data['count'];
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
		$sql = $this->db->getSQL($data);
		$company_id = $this->db->addCompany($sql);
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
		$sql = $this->db->getSQL($data);
		return $this->db->update($sql, "id = '$id'");
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