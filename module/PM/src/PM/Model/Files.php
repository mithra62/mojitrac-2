<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./module/PM/src/PM/Model/Files.php
 */

namespace PM\Model;

use Application\Model\AbstractModel;

 /**
 * PM - Files Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Files.php
 */
class Files extends AbstractModel
{
	
	/**
	 * The Files model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
		
	/**
	 * Returns the path to store files
	 * @return string
	 */
	public function getStoragePath()
	{
		return realpath($_SERVER['DOCUMENT_ROOT'].DS.'..'.DS.'media'.DS);
	}
	
	/**
	 * Returns all the allowed file extensions for uploaded files
	 * @return string
	 */
	public function getAllowedExtensions()
	{
		$settings = Zend_Registry::get('pm_settings');
		if($settings['allowed_file_formats'] != '')
		{
			return $settings['allowed_file_formats'];
		}
		else
		{
			return 'jpg,gif,png,txt,docx,doc,pdf,php,xls,xlsx,csv,psd,ppt,pptx,pot,potx,rar,zip,tar,gz,tgz,bz2,html,htm,avi,mov,fla,swf,asf,flv,sql,mp3';
	
		}
	}
	
	/**
	 * The maximum file size, in bytes, for an uploaded file
	 * @return string
	 */
	public function getMaxFileSize()
	{
		return '52428800';
	}
	
	/**
	 * Returns the File Form
	 * @return object
	 */
	public function getFileForm($options = array(), $disable_file = FALSE)
	{
        $form = new PM_Form_File($options, $disable_file);
        if(!$disable_file)
        {
        	$form->setAttrib('enctype', 'multipart/form-data');
        }
        return $form;		
	}

	/**
	 * Returns the File Revision Form
	 * @return object
	 */
	public function getFileRevisionForm($options = array())
	{
        $form = new PM_Form_File_Revision($options);
        $form->setAttrib('enctype', 'multipart/form-data');
        return $form;		
	}
	
	/**
	 * Returns the File Review Form
	 * @return object
	 */
	public function getFileReviewForm($options = array())
	{
        $form = new PM_Form_File_Review($options);
        return $form;		
	}
	
	/**
	 * Returns an array of all unique album names with artist names
	 * @return mixed
	 */
	public function getAllFiles($view_type = FALSE, array $where = null, array $not = null)
	{
		if($view_type)
		{
			if(!is_array($where))
			{
				$where = array();
			}
			$where['f.status'] = $view_type;
		}
		
		return $this->getFilesWhere($where, $not);			
	}
	
	public function getFileById($id)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('f'=>$this->db->getTableName()));
		
		$sql = $sql->where('f.id = ?', $id);
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = f.project_id', array('name AS project_name'));
		$sql = $sql->joinLeft(array('t' => 'tasks'), 't.id = f.task_id', array('name AS task_name'));
		$sql = $sql->joinLeft(array('c' => 'companies'), 'c.id = f.company_id', array('name AS company_name'));
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = f.owner', array('first_name AS file_owner_first_name', 'last_name AS file_owner_last_name'));
		
		return $this->db->getFile($sql);			
	}
	
	public function getFilesByCompanyId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['f.company_id'] = $id;
		return $this->getFilesWhere($where, $not);			
	}
	
	public function getFilesByProjectId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['f.project_id'] = $id;
		return $this->getFilesWhere($where, $not);				
	}
	
	public function getFilesByTaskId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['f.task_id'] = $id;
		return $this->getFilesWhere($where, $not);				
	}
	
	public function getFilesByUserId($id, array $where = null, array $not = null)
	{
		if(!is_array($where))
		{
			$where = array();
		}
		
		$where['f.owner'] = $id;
		return $this->getFilesWhere($where, $not);				
	}	
	
	private function getFilesWhere(array $where = null, array $not = null, array $orwhere = null, array $ornot = null)
	{
		$sql = $this->db->select()->from(array('f'=> 'files'));
		
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
		
		$sql = $sql->join(array('p' => 'projects'), 'p.id = f.project_id', array('project_name' => 'name'), 'left');
		$sql = $sql->join(array('t' => 'tasks'), 't.id = f.task_id', array('task_name' => 'name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = f.company_id', array('company_name' => 'name'), 'left');
		$sql = $sql->join(array('u' => 'users'), 'u.id = f.owner', array('file_owner_first_name' => 'first_name', 'file_owner_last_name' => 'last_name'), 'left');
		
		return $this->getRows($sql);	
		
	}	

	
	/**
	 * Inserts or updates a File
	 * @param $data
	 * @param $file_info
	 * @return mixed
	 */
	public function addFile($data, $file_info)
	{
		$sql = $this->db->getSQL($data);
		
		$file_info = $file_info['file'];		
		$path = $this->chmkdir($file_info['destination'], 
					   $data['company'], 
					   $data['project'], 
					   $data['task']
		);		
		
		$file_info['extension'] = $this->get_file_extension($file_info['tmp_name']);
		$file_info['stored_name'] = mktime().'.'.$file_info['extension'];
		$new_name = $path.DS.$file_info['stored_name'];
		if(!rename($file_info['tmp_name'],$new_name))
		{
			return FALSE;
		}
		
		$sql['company_id'] = (array_key_exists('company', $data) ? $data['company'] : 0);
		$sql['project_id'] = (array_key_exists('project', $data) ? $data['project'] : 0);
		$sql['task_id'] = (array_key_exists('task', $data) ? $data['task'] : 0);		
		
		if($data['file_id'] = $this->db->addFile($sql))
		{
			if(is_numeric($data['project_id']))
			{
				$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
				$project->updateProjectFileCount($data['project_id'], 1, 'file_count');
			}
			
			if(is_numeric($data['task_id']))
			{
				$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
				$task->updateTaskFileCount($data['task_id'], 1, 'file_count');
			}
			
			$file_info['description'] = 'First Import';
			$file_info['status'] = $data['status'];
			$file_info['uploaded_by'] = $data['uploaded_by'];
			$file_info['stored_path'] = $path;
			$file_info['revision_id'] = $this->addRevision($data['file_id'], $file_info);
			if($file_info['revision_id'] && isset($data['project']))
			{
				$noti = new PM_Model_Notifications;
				$noti->sendFileAdd($data, $file_info);					
			}			
		}
				
		return $data['file_id'];
	}
	
	/**
	 * Handles the processing of the images 
	 * @param string $name
	 * @param string $path
	 */
	public function processImage($name, $path, $image_data = FALSE)
	{
		if(!$image_data)
		{
			$image_data = getimagesize($path.DS.$name);
		}
		
		$imagick = new LambLib_Controller_Action_Helper_iMagick;
		if('image/psd' == $image_data['mime'])
		{
			$name = $imagick->convert_psd($path, $name, 'jpg');
		}
		
		$imagick->resize_image($path.DS.$name, $path.DS.'mid_'.$name, 700, FALSE);
		$imagick->resize_image($path.DS.$name, $path.DS.'tb_'.$name, 100, FALSE);
	}
	
	public function addRevision($id, array $file_info)
	{
		$db = new PM_Model_DbTable_File_Revisions;
		$file_info['file_name'] = $file_info['name'];
		$data['size'] = $file_info['size'];
		$file_info['mime_type'] = $file_info['type'];
		$sql = $db->getSQL($file_info);
		$sql['file_id'] = $id;
		
		$image_check = getimagesize($file_info['stored_path'].DS.$file_info['stored_name']);
		if($image_check)
		{
			if($sql['mime_type'] != $image_check['mime'])
			{
				$sql['mime_type'] = $image_check['mime'];
			}
			
			$this->processImage($file_info['stored_name'], $file_info['stored_path'], $image_check);
		}
		
		$rev_id = $db->addFileRevision($sql);
		if($rev_id)
		{
			$data = array('last_modified' => new Zend_Db_Expr('NOW()'));
			$this->db->update($data, 'id = '. $id);
			return $rev_id;
		}
	}
	
	public function addReview($data)
	{
		$file = new PM_Model_DbTable_File_Reviews;
		$sql = $file->getSQL($data);
		$sql['file_id'] = $data['file_id'];
		$sql['reviewer_id'] = $data['reviewer_id'];
		$sql['revision_id'] = $data['review_revision'];
		$review_id = $file->addFileReview($sql);
		if($rev_id)
		{
			$data = array('last_modified' => new Zend_Db_Expr('NOW()'));
			$this->db->update($data, 'id = '. $data['file_id']);
			return $rev_id;
		}		
	}
	
	public function get_file_extension($filename)
	{
	    $path_info = pathinfo($filename);
	    return $path_info['extension'];
	}
	
	public function getPreviewSize($view_size)
	{
		switch($view_size)
		{
			case 'mid':
			case 'tb':		
				$view_size = $view_size.'_';
			break;
			
			default:
				$view_size = 'mid_';
			break;
		}
		return $view_size;	
	}
	
	
	/**
	 * Updates a company
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateFile($data, $id)
	{
		$sql = $this->db->getSQL($data);
		return $this->db->update($sql, "id = '$id'");
	}
	
	/**
	 * Handles everything for a campaign to stop tracking a Last.fm Album Profile.
	 * @param $id
	 * @return bool
	 */
	public function removeFile($id)
	{
		$delete = $this->db->deleteFile($id);
		if($delete)
		{
			$rev = new PM_Model_DbTable_File_Revisions;
			$rev->deleteFileRevision($id, 'file_id');
			
			$reviews = new PM_Model_DbTable_File_Reviews;
			$reviews->deleteFileReview($id, 'file_id');
		}
		return $delete;
	}
	
	/**
	 * Removes all the tasks for the given $company_id
	 * @param int $company_id
	 * @return bool
	 */
	public function removeFilesByCompany($company_id)
	{
		return $this->db->deleteFile($company_id, 'company_id');		
	}

	/**
	 * Removes all the tasks for the given $project_id
	 * @param int $project_id
	 * @return bool
	 */
	public function removeFilesByProject($project_id)
	{
		return $this->db->deleteFile($project_id, 'project_id');		
	}	
	
	/**
	 * Returns the path to the media storage directory making sure the path exists, and creating it if not, along the way.
	 * @param string $start
	 * @param int $company_id
	 * @param int $project_id
	 * @param int $task_id
	 */
	public function chmkdir($start, $company_id, $project_id = FALSE, $task_id = FALSE)
	{
		$destination = $start.DS.$company_id;
		if($project_id)
		{
			$destination = $destination.DS.$project_id;
		}
		
		if($project_id && $task_id)
		{
			$destination = $destination.DS.$task_id;
		}
		
		$utilities = new LambLib_Controller_Action_Helper_Utilities;
		return $utilities->chkmkdir($destination);	
	}

	/**
	 * Returns all the revisions for a given $file_id
	 * @param int $file_id
	 */
	public function getFileRevisions($file_id)
	{
		$rev = new PM_Model_DbTable_File_Revisions;
		$sql = $rev->select()->setIntegrityCheck(false)->from(array('fr' => $rev->getTableName()))->where('file_id = ?', $file_id);
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = fr.uploaded_by', array('first_name AS uploader_first_name', 'last_name AS uploader_last_name'));
		return $rev->getFileRevisions($sql);
	}
	
	/**
	 * Returns all the reviews belonging to a $file
	 * @param int $file_id
	 */
	public function getFileReviews($file_id)
	{
		$reviews = new PM_Model_DbTable_File_Reviews;
		$sql = $reviews->select()->setIntegrityCheck(false)->from(array('fr' => $reviews->getTableName()))->where('file_id = ?', $file_id);
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = fr.reviewer_id', array('first_name AS reviewer_first_name', 'last_name AS reviewer_last_name'));
		return $reviews->getFileReviews($sql);
	}
	
	/**
	 * Returns the reviews for a given $id
	 * @param int $id
	 */
	public function getReviewById($id)
	{
		$reviews = new PM_Model_DbTable_File_Reviews;
		$sql = $reviews->select()->setIntegrityCheck(false)->from(array('fr' => $reviews->getTableName()))->where('fr.id = ?', $id);
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = fr.reviewer_id', array('first_name AS reviewer_first_name', 'last_name AS reviewer_last_name'));
		$sql = $sql->joinLeft(array('f' => 'files'), 'f.id = fr.file_id', array('name AS file_name'));
		return $reviews->getFileReview($sql);		
	}
	
	/**
	 * Returns all the reviews belonging to a given revision $id
	 * @param unknown_type $id
	 * @return mixed
	 */
	public function getReviewsByRevisionId($id)
	{
		$reviews = new PM_Model_DbTable_File_Reviews;
		$sql = $reviews->select()->setIntegrityCheck(false)->from(array('fr' => $reviews->getTableName()))->where('revision_id = ?', $id);
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = fr.reviewer_id', array('first_name AS reviewer_first_name', 'last_name AS reviewer_last_name'));
		return $reviews->getFileReviews($sql);		
	}
	
	/**
	 * Returns all the revision for a given $revision_id
	 * @param int $file_id
	 */
	public function getRevision($revision_id)
	{
		$rev = new PM_Model_DbTable_File_Revisions;
		$sql = $rev->select()->setIntegrityCheck(false)->from(array('fr' => $rev->getTableName()))->where('fr.id = ?', $revision_id);
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = fr.uploaded_by', array('first_name AS uploader_first_name', 'last_name AS uploader_last_name'));
		return $rev->getFileRevision($sql);
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
	
	public function removeReview($id)
	{
		$review = new PM_Model_DbTable_File_Reviews;
		return $review->deleteFileReview($id);
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