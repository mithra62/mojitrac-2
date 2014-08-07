<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Api/src/Api/Controller/ProjectsController.php
*/

namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend_Exception;

/**
 * Api - Projects Controller
 *
 * Projects REST API Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Controller/ProjectsController.php
 */
class ProjectsController extends AbstractRestfulJsonController
{
	/**
	 * Maps the available HTTP verbs we support for groups of data
	 * @var array
	 */
	protected $collectionOptions = array(
		'GET', 'POST', 'OPTIONS'
	);
	
	/**
	 * Maps the available HTTP verbs for single items
	 * @var array
	 */
	protected $resourceOptions = array(
		'GET', 'POST', 'DELETE', 'PUT', 'OPTIONS'
	);
		
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::getList()
	 */
	public function getList()
	{
		$company_id = $this->getRequest()->getQuery('company_id', false);
		$order = $this->getRequest()->getQuery('order', false);
		$order_dir = $this->getRequest()->getQuery('order_dir', false);
		$limit = $this->getRequest()->getQuery('limit', 10);
		$page = $this->getRequest()->getQuery('page', 1);
		
		if($company_id)
		{
			$company = $this->getServiceLocator()->get('Api\Model\Companies');
			$company_data = $company->getCompanyById($company_id);
			if(!$company_data)
			{
				$company_id = $company_data = FALSE;
			}
		}		

		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		if($company_id)
		{
			$project_data = $project->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getProjectsByCompanyId($company_id, FALSE);
				
		}
		else
		{
			if($this->perm->check($this->identity, 'manage_projects'))
			{
				$project_data = $project->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAllProjects(FALSE);
			}
			else
			{
				$user = $this->getServiceLocator()->get('Api\Model\Users');
				$project_data = $user->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAssignedProjects($this->identity);
			}
		}
		
		if(!empty($company_data))
		{
			$project_data = array_merge($company_data, $project_data);
		}
		
		if(empty($project_data['data']))
		{
			return $this->setError(404, 'not_found');
		}
		
		$project_data['data'] = $this->cleanCollectionOutput($project_data['data'], $project->projectOutputMap);
		return new JsonModel( $this->setupHalCollection($project_data, 'api-projects', 'projects', 'projects/view', 'project_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::get()
	 */
	public function get($id)
	{
		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		$project_data = $project->getProjectById($id);
		if(!$project_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		$project_data = $this->cleanResourceOutput($project_data, $project->projectOutputMap);
		
		$proj_team = $project->getProjectTeamMembers($id);
		$on_team = $project->isUserOnProjectTeam($this->identity, $id, $proj_team);
		if(!$on_team && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(404, 'not_found');
		}
		
		$embeds = array();
		$embeds['proj_team'] = $this->setupCollectionMeta($proj_team, 'api-users', 'users/view', 'user_id'); 
		return new JsonModel( $this->setupHalResource($project_data, 'api-projects', $embeds, 'projects/view', 'project_id') );
	}	
	
	public function create($data)
	{
		echo 'f';
		exit;
		return new JsonModel( );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::delete()
	 */
	public function delete($id)
	{
		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		$project_data = $project->getProjectById($id);
		if(!$project_data)
		{
			return $this->setError(404, 'not_found');
		}
	
		if(!$project->isUserOnProjectTeam($this->identity, $id) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		if(!$project->removeProject($id))
		{
			return $this->setError(500, 'project_remove_failed');
		}
	
		return new JsonModel( );
	}	
}
