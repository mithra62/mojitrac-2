<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/JsonController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
* PM - Json Controller
*
* Routes the Json requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/JsonController.php
*/
class JsonController extends AbstractPmController
{
	/**
	 * Handle the preDispatch
	 */
	public function preDispatch()
	{
    	$this->view->layout()->disableLayout();
    	$this->_helper->ViewRenderer->setNoRender(true);
    	
	    $autoloader = Zend_Loader_Autoloader::getInstance();
	    $autoloader->registerNamespace('PM');
	    
	    $this->identity = Zend_Auth::getInstance()->getIdentity();
        $this->perm = new PM_Model_Permissions;	    
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