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
		'GET', 'POST', 'DELETE', 'PATCH', 'PUT', 'OPTIONS'
	);
	
	/**
	 * The format for Project data we're goign to return
	 * @var unknown
	 */
	private $replaceMap = array(
		
	);
		
	public function getList()
	{
		return new JsonModel( array() );
	} 
	
	public function get($id)
	{
		return new JsonModel( array($id) );
	}	
}
