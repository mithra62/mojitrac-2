<?php
 /**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Files.php
 */

namespace PM\Model\Files;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

 /**
 * PM - Files Model
 *
 * @package 	Files
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Files.php
 */
class Revisions extends AbstractModel
{
	/**
	 * The form validation filering
	 * @var \Zend\InputFilter\InputFilter
	 */
	protected $input_filter;
	
	/**
	 * The Transfer Adapter for validating and moving uploaded files
	 * @var \Zend\File\Transfer\Adapter\Http
	 */
	protected $file_transfer_adapter;
	
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
	 * Returns an array for modifying $_name
	 * @param $data
	 * @return array
	 */
	public function getSQL($data){
		return array(
			'file_name' => $data['file_name'],
			'stored_name' => $data['stored_name'],
			'size' => $data['size'],
			'extension' => $data['extension'],
			'mime_type' => $data['mime_type'],
			'description' => $data['description'],
			'status' => $data['status'],
			'approver' => $data['approver'],
			'uploaded_by' => $data['uploaded_by'],
			'approval_comment' => $data['approval_comment'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}	
	
	/**
	 * Sets the input filter
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	public function getInputFilter($file_field = false)
	{
		if (!$this->input_filter) {
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

			if($file_field)
			{
				$inputFilter->add($factory->createInput(array(
					'name'     => 'file_upload',
					'required' => true,
					'messages' => array(
	                	\Zend\Validator\NotEmpty::IS_EMPTY => 'fdsafdsa'
					),
				)));	
			}	
			
	
			$this->input_filter = $inputFilter;
		}
	
		return $this->input_filter;
	}
	
	public function addRevision($id, array $file_info)
	{
		$file_info['file_name'] = $file_info['name'];
		$data['size'] = $file_info['size'];
		$file_info['mime_type'] = $file_info['type'];
		$sql = $this->getSQL($file_info);
		$sql['file_id'] = $id;
		
		$image_check = getimagesize($file_info['stored_path'].DS.$file_info['stored_name']);
		if($image_check)
		{
			if($sql['mime_type'] != $image_check['mime'])
			{
				$sql['mime_type'] = $image_check['mime'];
			}
			
			//$this->processImage($file_info['stored_name'], $file_info['stored_path'], $image_check);
		}
		
		return $this->insert('file_revisions', $sql);
	}

	/**
	 * Returns all the revisions for a given $file_id
	 * @param int $file_id
	 */
	public function getFileRevisions($file_id)
	{
		$sql = $this->db->select()->from(array('fr' => 'file_revisions'))->where(array('file_id' => $file_id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = fr.uploaded_by', array('uploader_first_name' => 'first_name','uploader_last_name' => 'last_name'), 'left');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns all the revision for a given $revision_id
	 * @param int $file_id
	 */
	public function getRevision($revision_id)
	{
		$sql = $this->db->select()->from(array('fr' => 'file_revisions'))->where(array('fr.id' => $revision_id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = fr.uploaded_by', array('uploader_first_name' => 'first_name', 'uploader_last_name' => 'last_name'), 'left');
		return $this->getRow($sql);
	}
	
	/**
	 * Deletes a revision entry based on the pk
	 * @param int $id
	 */
	public function removeRevision($id)
	{
		$rev = new PM_Model_DbTable_File_Revisions;
		return $rev->deleteFileRevision($id);
	}
	
	public function getTotalFileRevisions($id)
	{
		$rev = new PM_Model_DbTable_File_Revisions;
		$sql = $rev->select()->from(array('fr' => $rev->getTableName()), array(new Zend_Db_Expr('COUNT(id) AS Count')))->where('fr.file_id = ?', $id);
		$total = $rev->getFileRevision($sql);
		if($total)
		{
			return $total['Count'];
		}
	}
	
}