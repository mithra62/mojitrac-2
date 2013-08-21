<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/JsonController.php
*/

/**
* PM - Json Controller
*
* Routes the Json requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/JsonController.php
*/
class PM_JsonController extends Zend_Controller_Action
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