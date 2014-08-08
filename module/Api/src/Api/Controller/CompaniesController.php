<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Api/src/Api/Controller/CompaniesController.php
*/

namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use Zend_Exception;

/**
 * Api - Companies Controller
 *
 * Companies REST API Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Controller/CompaniesController.php
 */
class CompaniesController extends AbstractRestfulJsonController
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
	 * Class preDispatch
	 */
	public function onDispatch( \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
		if(!parent::check_permission('view_companies'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
	
		return $e;
	}	
		
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::getList()
	 */
	public function getList()
	{
		$order = $this->getRequest()->getQuery('order', false);
		$order_dir = $this->getRequest()->getQuery('order_dir', false);
		$limit = $this->getRequest()->getQuery('limit', 10);
		$page = $this->getRequest()->getQuery('page', 1);
		
		$company = $this->getServiceLocator()->get('Api\Model\Companies');
		if($this->perm->check($this->identity, 'manage_companies'))
		{
			$company_data = $company->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAllCompanies(FALSE);
		}
		else
		{
			$user = $this->getServiceLocator()->get('Api\Model\Users'); 
			$company_data = $user->setLimit($limit)->setOrderDir($order_dir)->setOrder($order)->setPage($page)->getAssignedProjectCompanies($this->identity);
		}

		$company_data['data'] = $this->cleanCollectionOutput($company_data['data'], $company->companiesOutputMap);
		return new JsonModel( $this->setupHalCollection($company_data, 'api-companies', 'companies', 'companies/view', 'company_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::get()
	 */
	public function get($id)
	{
		$company = $this->getServiceLocator()->get('Api\Model\Companies');
		$company_data = $company->getCompanyById($id);
		if(!$company_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		$company_data = $this->cleanResourceOutput($company_data, $company->companiesOutputMap);
		return new JsonModel( $this->setupHalResource($company_data, 'api-companies', array(), 'companies/view', 'company_id') );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::create()
	 */
	public function create($data)
	{
		if(!parent::check_permission('manage_companies'))
		{
			return $this->setError(403, 'unauthorized_action');
		}

		$company = $this->getServiceLocator()->get('Api\Model\Companies');

		//we have to validate the data has everything we need
		$inputFilter = $company->getInputFilter();
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
		
		$data['creator'] = $this->identity;
		$company_id = $company->addCompany($data);
		if(!$company_id)
		{
			return $this->setError(500, 'company_create_failed');
		}
		
		$company_data = $company->getCompanyById($company_id);
		
		$company_data = $this->cleanResourceOutput($company_data, $company->companiesOutputMap);
		return new JsonModel( $this->setupHalResource($company_data, 'api-companies', array(), 'companies/view', 'company_id') );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::delete()
	 */
	public function delete($id)
	{
		if(!parent::check_permission('manage_companies'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$company = $this->getServiceLocator()->get('Api\Model\companies');
		$company_data = $company->getCompanyById($id);
		if(!$company_data)
		{
			return $this->setError(404, 'not_found');
		}
		
		if(!$company->removeCompany($id))
		{
			return $this->setError(500, 'company_remove_failed');
		}
	
		return new JsonModel( );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::update()
	 */
	public function update($id, $data)
	{
		$project = $this->getServiceLocator()->get('Api\Model\Projects');
		$project_data = $project->getProjectById($id);
	
		if (!$project_data)
		{
			return $this->setError(404, 'not_found');
		}
	
		if(!$project->isUserOnProjectTeam($this->identity, $id) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'unauthorized_action');
		}
		
		$inputFilter = $project->getInputFilter();
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'missing_input_data', null, null, array('errors' => $inputFilter->getMessages()));
		}
	
		$data = array_merge($project_data, $data);
	
		try {
				
			$project->updateProject($data, $id);
				
		} catch(Zend_Exception $e)
		{
			return $this->setError(500, 'project_update_failed');
		}
	
		$project_data = $project->getProjectById($id);
		$project_data = $this->cleanResourceOutput($project_data, $project->projectOutputMap);
		$proj_team = $this->cleanCollectionOutput($project->getProjectTeamMembers($id), $project->projectTeamOutputMap);

		$embeds = array();
		$embeds['proj_team'] = $this->setupCollectionMeta($proj_team, 'api-users', 'users/view', 'user_id');
		
		$project_data['project_id'] = $id;
		return new JsonModel( $this->setupHalResource($project_data, 'api-projects', $embeds, 'projects/view', 'project_id') );
	}	
}
