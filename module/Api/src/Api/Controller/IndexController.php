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
    
    public function chainProjectsAction()
    {
    	$company_id = (int)$this->_request->getParam('_value', FALSE);
    	if($company_id == 0)
    	{
    		return FALSE;
    	}
    
    	if($this->perm->check($this->identity, 'manage_projects') && $this->perm->check($this->identity, 'view_companies'))
    	{
    		$project = new PM_Model_Projects(new PM_Model_DbTable_Projects);
    		$projects = $project->getProjectOptions($company_id);
    	}
    	else
    	{
    		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
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
    
    	echo Zend_Json::encode( $arr );
    }
    
    public function chainTasksAction()
    {
    	$project_id = (int)$this->_request->getParam('_value', FALSE);
    	if($project_id == 0)
    	{
    		return FALSE;
    	}
    
    	if($this->perm->check($this->identity, 'manage_tasks'))
    	{
    		$task = new PM_Model_Tasks(new PM_Model_DbTable_Tasks);
    		$tasks = $task->getTaskOptions($project_id);
    	}
    	else
    	{
    		$user = new PM_Model_Users(new PM_Model_DbTable_Users);
    		$tasks = $user->getOpenAssignedTasks($this->identity, $project_id);
    	}
    
    	$arr = array();
    	$arr[] = array('none' => '');
    	foreach($tasks AS $task)
    	{
    		$arr[] = array($task['id'] => $task['name']);
    	}
    
    	echo Zend_Json::encode( $arr );
    }    
}
