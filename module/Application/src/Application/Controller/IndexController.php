<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./moji/application/controllers/LoginController.php
 */

namespace Application\Controller;

use Application\Controller\AbstractController;

/**
 * Default - Login Class
 *
 * Handles login routing
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./moji/application/controllers/LoginController.php
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
