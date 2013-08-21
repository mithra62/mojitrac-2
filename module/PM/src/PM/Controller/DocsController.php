<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/DocsController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Docs Controller
*
* Routes the Docs requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/DocsController.php
*/
class Pm_DocsController extends PM_Abstract
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