<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Api/src/Api/Controller/TasksController.php
*/

namespace Api\Controller;

use Api\Controller\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

/**
 * Api - Tasks Controller
 *
 * Tasks REST API Controller
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Api/src/Api/Controller/TasksController.php
 */
class TasksController extends AbstractRestfulJsonController
{
	public function getList()
	{
		return new JsonModel( array() );
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
