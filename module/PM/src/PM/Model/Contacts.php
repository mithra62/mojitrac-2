<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Projects.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

 /**
 * PM - ProjectForm Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Projects.php
 */
class Contacts extends AbstractModel
{
	
	/**
	 * The form validation filering
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $inputFilter;

	/**
	 * The Project Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Sets the input filter to use
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns the InputFilter
	 * @return \Zend\InputFilter\InputFilter
	 */
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
	 * Returns the Contact Form
	 * @return object
	 */
	public function getContactForm($options = array(), $hidden = array())
	{
        return new PM_Form_Contact($options, $hidden);		
	}	
	
	/**
	 * Returns the $mbid for a given artist $name
	 * @param $name
	 * @return mixed
	 */
	public function getContactIdByName($name)
	{
		$sql = $this->db->select()
					  ->from($contact->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $contact->getContact($sql);
	}
	
	public function getContactById($id)
	{
		$sql = $contact->select()->setIntegrityCheck(false)->from(array('c'=>$contact->getTableName()));
		$sql = $sql->where('c.id = ?', $id);
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = c.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
		$sql = $sql->joinLeft(array('o' => 'companies'), 'o.id = c.company_id', array('name AS company_name'));
		return $contact->getContact($sql);
	}
	
	/**
	 * Returns an array of all unique artist names
	 * @return mixed
	 */
	public function getAllContactNames()
	{
		$contact = new PM_Model_DbTable_Contacts;
		$sql = $contact->select()->from($contact->getTableName(), array('id','name'));
		return $contact->getCompanies($sql);
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllContacts($view_type = FALSE)
	{
		$contacts = new PM_Model_DbTable_Contacts;
		$sql = $contacts->select();
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where('type = ?', $view_type);
		}
		return $contacts->getContacts($sql);		
	}
	
	/**
	 * Returns all the times for a given $id
	 * @param int $id
	 * @return array
	 */
	public function getContactsByCompanyId($id)
	{
		$sql = $this->db->select()->from(array('c'=>'company_contacts'));
		
		$sql = $sql->where(array('c.company_id' => $id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = c.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		
		return $this->getRows($sql);			
	}	
	
	/**
	 * Inserts or updates a Company
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addContact($data)
	{
		$contact = new PM_Model_DbTable_Contacts;
		$sql = $contact->getSQL($data);
		$contact_id = $contact->addContact($sql);
		return $contact_id;
	}
	
	/**
	 * Updates a company
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateContact($data, $id)
	{
		$contact = new PM_Model_DbTable_Contacts;
		$sql = $contact->getSQL($data);
		return $contact->update($sql, "id = '$id'");
	}
	
	/**
	 * Handles everything for removing a company.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeContact($id)
	{
		$contact = new PM_Model_DbTable_Contacts;
		if($contact->deleteContact($id))
		{
			/*
			$projects = new PM_Model_Projects(new PM_Model_DbTable_Projects);
			$projects->removeProjectsByCompany($id);
			
			$tasks = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$tasks->removeTasksByCompany($id);
			
			$files = new PM_Model_Files(new PM_Model_DbTable_Files);
			$files->removeFilesByCompany($id);

			$notes = new PM_Model_Notes;
			$notes->removeNotesByCompany($id);

			$bookmarks = new PM_Model_Bookmarks;
			$bookmarks->removeBookmarksByCompany($id);	
			*/		
		}
		
		return TRUE;
	}
}