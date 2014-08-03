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
}
