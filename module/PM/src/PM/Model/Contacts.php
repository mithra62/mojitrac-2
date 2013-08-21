<?php
/**
 * Contacts Model
 * @author Eric
 *
 */
class PM_Model_Contacts
{
	
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
		$contact = new PM_Model_DbTable_Contacts;
		$sql = $contact->select()
					  ->from($contact->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $contact->getContact($sql);
	}
	
	public function getContactById($id)
	{
		$contact = new PM_Model_DbTable_Contacts;
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
		$contact = new PM_Model_DbTable_Contacts();
		$sql = $contact->select()->setIntegrityCheck(false)->from(array('c'=>$contact->getTableName()));
		
		$sql = $sql->where('c.company_id = ?', $id);
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = c.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
		
		return $contact->getContacts($sql);			
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