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
		'GET', 'OPTIONS'
	);
	
	/**
	 * Maps the available HTTP verbs for single items
	 * @var array
	 */
	protected $resourceOptions = array(
		'GET', 'POST', 'DELETE', 'PUT', 'OPTIONS'
	);
		
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
			$project_data = $project->setLimit($limit)
									  ->setOrderDir($order_dir)
									  ->setOrder($order)
									  ->setPage($page)
									  ->getProjectsByCompanyId($company_id, FALSE);
				
		}
		else
		{
			if($this->perm->check($this->identity, 'manage_projects'))
			{
				$project_data = $project->setLimit($limit)
										  ->setOrderDir($order_dir)
										  ->setOrder($order)
										  ->setPage($page)
										  ->getAllProjects(FALSE);
			}
			else
			{
				$user = $this->getServiceLocator()->get('Api\Model\Users');
				$project_data = $user->setLimit($limit)
									  ->setOrderDir($order_dir)
									  ->setOrder($order)
									  ->setPage($page)->getAssignedProjects($this->identity);
			}
		}
		
		if(!empty($company_data))
		{
			$project_data = array_merge($company_data, $project_data);
		}
		
		$project_data['data'] = $this->cleanCollectionOutput($project_data['data'], $project->projectOutputMap);
		return new JsonModel( $this->setupHalCollection($project_data, 'api-projects', 'projects', 'projects/view', 'project_id') );
	}
	
	public function get($id)
	{
		return new JsonModel( array($id) );
	}	
}
