<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Controller/CliController.php
 */

namespace PM\Controller;

use Application\Controller\AbstractController;

/**
 * PM - Command Line Controller
 *
 * Handles the PM module Console requests
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Controller/CliController.php
 */
class CliController extends AbstractController
{
    public function archiveTasksAction()
    {
    	$days = $this->params()->fromRoute('days', 7);
    	$status = $this->params()->fromRoute('status', 6);
    	$verbose = $this->params()->fromRoute('verbose');
    	if($verbose)
    	{
    		echo 'verbose';
    	}
    	
    	$task = $this->getServiceLocator()->get('PM\Model\Tasks');
    	$return = $task->autoArchive($days, $status);  
    	if($verbose)
    	{
    		return $return;
    	}
    	
    }    
}
