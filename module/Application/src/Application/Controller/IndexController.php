<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Application/src/Application/Controller/IndexController.php
 */

namespace Application\Controller;

use Application\Controller\AbstractController;

/**
 * Application - Index Controller
 *
 * Just placeholder and redict
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Application/src/Application/Controller/IndexController.php
 */
class IndexController extends AbstractController
{
    public function indexAction()
    {
        return $this->redirect()->toRoute('login');
    }
    
    public function phpInfoAction()
    {
    	phpinfo();
    	exit;
    }    
}
