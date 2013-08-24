<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Controller/DocsController.php
*/

namespace PM\Controller;

use PM\Controller\AbstractPmController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
/**
* PM - Index Controller
*
* Routes the Home requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Controller/DocsController.php
*/
class DocsController extends AbstractPmController
{
	
	private $page;

	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        $this->view->headTitle('Documentation', 'PREPEND');
        $this->view->layout_style = 'left';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'docs';
        $this->view->uri = $this->_request->getPathInfo();
		$this->view->active_sub = 'None';
		$this->view->title = FALSE;
		$this->page = $this->view->page = $this->_getParam("page",FALSE);      
	}
	
    
    public function indexAction()
    {
    	
    }
    
    public function projectsAction()
    {
    	$this->view->project_filter = $this->_getParam("view",FALSE);
    }
    
    public function companiesAction()
    {
    	
    }
    
    public function tasksAction()
    {

    }
    
    public function contactsAction()
    {
    	
    }
    
    public function timesAction()
    {
    	
    }
    
    public function ipsAction()
    {
    	
    }
    
    public function settingsAction()
    {
    	
    }    
    
    public function rolesAction()
    {
    	
    }
    
    public function usersAction()
    {
    	
    }
    
    public function calendarAction()
    {
    	
    }
    
}