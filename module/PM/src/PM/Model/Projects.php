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

use Application\Model\AbstractModel;

 /**
 * PM - Projects Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Projects.php
 */
class Projects extends AbstractModel
{
	/**
	 * The key to use for the cache items
	 * @var string
	 */
	public $cache_key = 'projects';
	
	/**
	 * The Projects Model
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	/**
	 * Returns the Project Form
	 * @return object
	 */
	public function getProjectForm($options = array(), $hidden = array())
	{
        return new PM_Form_Project($options, $hidden);		
	}	
	
	/**
	 * Returns the project for a given $name
	 * @param $name
	 * @return mixed
	 */
	public function getProjectIdByName($name)
	{
		$sql = $this->db->select()
					  ->from($this->db->getTableName(), array('id'))
					  ->where('name LIKE ?', $name);
					  
		return $this->db->getProject($sql);
	}
	
	/**
	 * Returns the $company_id for a give project $id
	 * @param int $id
	 * @return array
	 */
	public function getCompanyIdById($id)
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('company_id'))->where('id = ?', $id);
		return $this->db->getProject($sql);
	}
	
	/**
	 * Returns a project by the $id
	 * @param int $id
	 * @return array
	 */
	public function getProjectById($id, $what = null)
	{
		$sql = $this->db->select();
		if(is_array($what))
		{
			$sql->from(array('p'=> 'projects'))->columns($what);
		}
		else
		{
			$sql->from(array('p'=> 'projects'));
		}
				
		$sql = $sql->where(array('p.id' => $id));
		
		$sql = $sql->join(array('u' => 'users'), 'u.id = p.creator', array('creator_first_name' => 'first_name', 'creator_last_name' => 'last_name'), 'left');
		$sql = $sql->join(array('c' => 'companies'), 'c.id = p.company_id', array('company_name' => 'name'), 'left');
		return $this->getRow($sql);
	}
	
	/**
	 * Returns a project by the $id
	 * @param int $id
	 * @return array
	 */
	public function getProjectByHarvestId($harvest_id)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('p'=>$this->db->getTableName()))->where('p.harvest_id = ?', $harvest_id);
		
		$sql = $sql->joinLeft(array('u' => 'users'), 'u.id = p.creator', array('first_name AS creator_first_name', 'last_name AS creator_last_name'));
		$sql = $sql->joinLeft(array('c' => 'companies'), 'c.id = p.company_id', array('name AS company_name'));
		return $this->db->getProject($sql);
	}

	/**
	 * Returns a companies projects by the $id
	 * @param $id
	 * @return array
	 */
	public function getProjectsByCompanyId($id, $exclude_archive = FALSE)
	{
		$sql = $this->db->select()->where('company_id = ?', $id);
		if($exclude_archive)
		{
			$sql = $sql->where('status != ?', '6');
		}		
		return $this->db->getProjects($sql);
	}

	/**
	 * Returns all the projects that start on a given date
	 * @param string $date
	 */
	public function getProjectsByStartDate($date)
	{
		$sql = $this->db->select()->setIntegrityCheck(false)
						->from(array('p'=>$this->db->getTableName()))
						->where('start_date = ?', $date);
						
		$sql = $sql->joinLeft(array('companies'), 'p.company_id = companies.id', array('name AS company_name'));
		
		return $this->db->getProjects($sql);
	}
	
	/**
	 * Returns an array of all unique artist names
	 * @return mixed
	 */
	public function getAllProjectNames()
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('name'))
								->where('status = ?', 'active');
		return $this->db->getProjects($sql);
	}
	
	/**
	 * Returns an array of all projects filtered by $view_type
	 * @return mixed
	 */
	public function getAllProjects($view_type = FALSE)
	{
		$sql = $this->db->select()->from(array('p'=> 'projects'));
		
		if(is_numeric($view_type))
		{
			$sql = $sql->where(array('p.status' => $view_type));
		} else {
			
			$sql = $sql->where(array('p.status != 6'));
		}
		
		$sql = $sql->join('companies', 'p.company_id = companies.id', array('company_name' => 'name'), 'left');
		return $this->getRows($sql);		
	}
	
	/**
	 * Returns the project name and id for a given $company_id. 
	 * @param int $company_id
	 * @return array
	 */
	public function getProjectOptions($company_id = FALSE, $identity = FALSE)
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('name', 'id'));
		if($company_id)
		{
			$sql = $sql->where('company_id = ?', $company_id);
		}
		
		$sql = $sql->order('name ASC');
		
		return $this->db->getProjects($sql);
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
					->from('tasks')->columns(array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where(array('project_id' => $id));
		$data = $this->getRow($sql);
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
		$sql = $this->db->select()
					->from('files')->columns( array(new \Zend\Db\Sql\Expression('COUNT(id) AS count')))
					->where(array('project_id' => $id));
		$data = $this->getRow($sql);
		if(is_array($data))
		{
			return $data['count'];
		}		
	}

	/**
	 * Returns the company_id for a given project $id
	 * @param int $id
	 * @return int
	 */
	public function getCompanyId($id)
	{
		$sql = $this->db->select()->from($this->db->getTableName(), array('company_id'))
						->where('id = ?', $id);
		$company = $this->db->getProject($sql);
		
		if(array_key_exists('company_id', $company))
		{
			return $company['company_id'];
		}
	}

	
	/**
	 * Inserts or updates a Company
	 * @param $data
	 * @param $bypass_update
	 * @return mixed
	 */
	public function addProject($data, $bypass_update = FALSE)
	{
		$ext = $this->event('pre.moji_project_add', $this, compact('data'));
		if($ext->stopped()) return $ext->last();
				
		$sql = $this->db->getSQL($data);
		$project_id = $this->db->addProject($sql);
		
		$ext = $this->event('post.moji_project_add', $this, compact('project_id', 'data'));
		if($ext->stopped()) return $ext->last();
				
		return $project_id;
	}
	
	/**
	 * Updates a project
	 * @param array $data
	 * @param int	 $id
	 * @return bool
	 */
	public function updateProject($data, $id)
	{
		$ext = $this->event('pre.moji_project_update', $this, compact('data', 'id'));
		if($ext->stopped()) return $ext->last();
				
		$sql = $this->db->getSQL($data);
		$return = $this->db->update($sql, "id = '$id'");
		
		$ext = $this->event('post.moji_project_update', $this, compact('data', 'id'));
		if($ext->stopped()) return $ext->last();	

		return $return;
	}
	
	/**
	 * Updates the task count for a given $id
	 * @param int 	 $id
	 * @param int 	 $count
	 * @param string $col
	 * @return bool
	 */
	public function updateProjectTaskCount($id, $count = 1, $col = 'task_count')
	{
		$sql = array($col => new Zend_Db_Expr($col.'+'.$count));
		return $this->db->updateProject($sql, $id);
	}
	
	/**
	 * Updates the hours_worked for a given $id
	 * @param int 	$id
	 * @param floar $time
	 * @return bool
	 */
	public function updateProjectTime($id, $time)
	{
		$sql = array('hours_worked' => new Zend_Db_Expr('hours_worked+'.$time));
		return $this->db->updateProject($sql, $id);		
	}
	
	/**
	 * Updates the file count for a given $id
	 * @param int 	 $id
	 * @param int 	 $count
	 * @param string $col
	 * @return bool
	 */
	public function updateProjectFileCount($id, $count = 1, $col = 'task_count')
	{
		$sql = array($col => new Zend_Db_Expr($col.'+'.$count));
		return $this->db->updateProject($sql, $id);
	}	
	
	/**
	 * Handles everything for a campaign to stop tracking a Last.fm Album Profile.
	 * @param $id
	 * @param $campaign_id
	 * @return bool
	 */
	public function removeProject($id)
	{	
		$ext = $this->event('pre.moji_project_remove', $this, compact('id'));
		if($ext->stopped()) return $ext->last();
				
		$company_id = $this->getCompanyId($id);
		if($this->db->deleteProject($id))
		{
			$tasks = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
			$tasks->removeTasksByProject($id);
			
			$files = new PM_Model_Files(new PM_Model_DbTable_Files);
			$files->removeFilesByProject($id);

			$notes = new PM_Model_Notes;
			$notes->removeNotesByProject($id);

			$bookmarks = new PM_Model_Bookmarks(new PM_Model_DbTable_Bookmarks);
			$bookmarks->removeBookmarksByProject($id);
			
			$companies = new PM_Model_Companies(new PM_Model_DbTable_Companies);
			$companies->updateCompanyProjectCount($company_id, -1, 'active_projects');
			
			$ext = $this->event('post.moji_project_remove', $this, compact('id'));
			if($ext->stopped()) return $ext->last();
						
			return TRUE;
		}
	}
	
	/**
	 * Removes all the projects for the given $company_id
	 * @param int $company_id
	 * @return bool
	 */
	public function removeProjectsByCompany($company_id)
	{
		return $this->db->deleteProject($company_id, 'company_id');		
	}
	
	/**
	 * Returns all the users, with info, attached to a particular team
	 * @param int $id
	 * @return array
	 */
	public function getProjectTeamMembers($project_id)
	{
		$sql = $this->db->select()->from(array('pt'=> 'project_teams'));
		$sql = $sql->where(array('project_id' => $project_id));
		$sql = $sql->join(array('u' => 'users'), 'u.id = pt.user_id', array('first_name', 'last_name', 'email', 'job_title', 'user_id' => 'id'), 'left');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns just the $user_ids for all the users attached to team_id
	 * @param int $id
	 * @return array
	 */
	public function getProjectTeamMemberIds($id)
	{
		$sql = $this->db->select()->from(array('pt'=> 'project_teams'))->columns(array('user_id'))->where(array('project_id' => $id));
		$members = $this->getRows($sql);
		$_members = array();
		foreach($members AS $member)
		{
			$_members[] = $member['user_id'];
		}
		return $_members;
	}
	
	/**
	 * Adds a user to the projec team
	 * @param $id
	 * @param $project
	 * @return bool
	 */
	public function addProjectTeamMember($id, $project)
	{
		$sql = array(
			'project_id' => $project,
			'user_id' => $id,
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()'),
			'created_date' => new \Zend\Db\Sql\Expression('NOW()')
		);
		
		return $this->insert('project_teams', $sql);
	}
	
	/**
	 * Removes a user from a project team
	 * @param int $id
	 * @param int $project
	 * @return unknown_type
	 */
	public function removeProjectTeamMember($id, $project)
	{
		$team = new PM_Model_DbTable_Projects_Teams;
		$where = "user_id = '$id' AND project_id = '$project'";
		return $team->delete($where);
	}
	
	/**
	 * Removes the entire project team from $id
	 * @param int $id
	 */
	public function removeProjectTeam($id)
	{
		$team = new PM_Model_DbTable_Projects_Teams;
		return $team->deleteProjectTeamMember($id, 'project_id');		
	}
	
	/**
	 * Checks if the given $user is on $project. Optionally, send the project team array along with the request (if available)
	 * @param int $user
	 * @param int $project
	 * @param array $project_teams
	 * @return bool
	 */
	public function isUserOnProjectTeam($user, $project, array $project_teams = null)
	{
		if(null === $project_teams)
		{
			$project_teams = $this->getProjectTeamMembers($project);
		}
		foreach($project_teams AS $team_member)
		{
			if($user == $team_member['user_id'])
			{
				return TRUE;
			}
		}
	}
	
	public function getByCompanyIdProjectName($name, $company_id)
	{
		$sql = $this->db->select()->from(array('p' => $this->db->getTableName()), array('id'))->where('company_id = ?', $company_id)->where('name LIKE ?', $name);
		return $this->db->getProject($sql);
	}
}