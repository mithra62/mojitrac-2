<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Api/src/Api/Controller/IndexController.php
*/

namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

/**
 * Api - Index Controller
 *
 * General API Interaction Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Controller/IndexController.php
 */
class IndexController extends AbstractRestfulJsonController
{
    public function indexAction()
    {
    	return new JsonModel(array('data' => "The MojiTrac API is alive."));
    }  
    
    /**
     * Creates a JSON array to feed to drop down fields
     * @return \Zend\View\Model\JsonModel
     */
    public function chainProjectsAction()
    {
    	$company_id = $this->getRequest()->getQuery('_value', false);
    	if($company_id == 0)
    	{
    		return $this->setError(422, 'Bad, or no, company_id parameter');
    	}
    
    	if($this->perm->check($this->identity, 'manage_projects') && $this->perm->check($this->identity, 'view_companies'))
    	{
    		$project = $this->getServiceLocator()->get('PM\Model\Projects');
    		$projects = $project->getProjectOptions($company_id);
    	}
    	else
    	{
    		$user = $this->getServiceLocator()->get('PM\Model\Users');
    		$projects = $user->getAssignedProjects($this->identity);
    		$arr = array();
    		$count = 0;
    		foreach($projects AS $project)
    		{
    			if($project['company_id'] == $company_id)
    			{
    				$arr[$count]['id'] = $project['id'];
    				$arr[$count]['name'] = $project['name'];
    				$count++;
    			}
    		}
    		$projects = $arr;
    	}
    
    	$arr = array();
    	$arr[] = array('none' => '');
    	foreach($projects AS $project)
    	{
    		$arr[] = array($project['id'] => $project['name']);
    	}
    
    	return new JsonModel( $arr );
    }    
    
    /**
     * Creates a JSON array to feed to drop down fields
     * @return \Zend\View\Model\JsonModel
     */
    public function chainTasksAction()
    {
    	$project_id = (int)$this->getRequest()->getQuery('_value', false);
    	if($project_id == 0)
    	{
    		return $this->setError(422, 'Bad, or no, project_id parameter');
    	}
    
    	if($this->perm->check($this->identity, 'manage_tasks'))
    	{
    		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    		$tasks = $task->getTaskOptions($project_id);
    	}
    	else
    	{
    		$user = $this->getServiceLocator()->get('PM\Model\Users');
    		$tasks = $user->getOpenAssignedTasks($this->identity, $project_id);
    	}
    
    	$arr = array();
    	$arr[] = array('none' => '');
    	foreach($tasks AS $task)
    	{
    		$arr[] = array($task['id'] => $task['name']);
    	}
    
    	return new JsonModel( $arr );
    }    
}
