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
		'GET', 'POST', 'DELETE', 'PATCH', 'PUT', 'OPTIONS'
	);
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::getList()
	 */
	public function getList()
	{
		return new JsonModel( array() );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::get()
	 */
	public function get($id)
	{
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$task_data = $task->getTaskById($id);
		if (!$task_data)
		{
			return $this->setError(404, 'Not Found');
		}
	
		if($task_data['assigned_to'] == $this->identity)
		{
			$view['assigned_to'] = TRUE;
		}
	
		return new JsonModel( array($task_data) );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::create()
	 */
	public function create($data)
	{
		if(empty($data['project_id']))
		{
			return $this->setError(422, 'Invalid project_id parameter');
		}
		
		//make sure we're dealing with a valid project
		$projects = $this->getServiceLocator()->get('PM\Model\Projects');
		$project_data = $projects->getProjectById($data['project_id']);
		if(!$project_data)
		{
			return $this->setError(422, 'Invalid project_id parameter');
		}

		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		
		//we have to validate the data has everything we need
		$inputFilter = $task->getInputFilter();
		$inputFilter->setData($data);
		if (!$inputFilter->isValid($data))
		{
			return $this->setError(422, 'Missing input data', null, null, array('errors' => $inputFilter->getMessages()));
		}
		
		//now we can add it to the db...
		$data['creator'] = $this->identity;
		$task_id = $task->addTask($data);
		if(!$task_id)
		{
			return $this->setError(500, 'Task create failed!');
		}
		
		$this->setStatusCode(201);
		
		//and now let's pull the created task for the response
		$task_data = $task->getTaskById($task_id);
		return new JsonModel( $task_data );
	}  
	
	/**
	 * (non-PHPdoc)
	 * @see \Api\Controller\AbstractRestfulJsonController::delete()
	 */
	public function delete($id)
	{
		$task = $this->getServiceLocator()->get('PM\Model\Tasks');
		$task_data = $task->getTaskById($id);
		
		if(!$task_data)
		{
			return $this->setError(404, 'Not found');
		}

		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->redirect()->toRoute('tasks/view', array('task_id' => $id));
		}		
		
		$project = $this->getServiceLocator()->get('PM\Model\Projects');
		if(!$project->isUserOnProjectTeam($this->identity, $task_data['project_id']) && !$this->perm->check($this->identity, 'manage_projects'))
		{
			return $this->setError(403, 'Unauthorized to perform that action');
		}
		
		if(!$task->removeTask($id))
		{
			return $this->setError(500, 'Task remove failed!');
		}
			
		return new JsonModel( array() );
	}	
}
