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
	public function onDispatch(  \Zend\Mvc\MvcEvent $e )
	{
		$e = parent::onDispatch( $e );
        //$this->view->headTitle('Documentation', 'PREPEND');
        $this->layout()->setVariable('layout_style', 'left');
        $this->layout()->setVariable('sidebar', 'dashboard');
        $this->layout()->setVariable('sub_menu', 'docs');
        //$this->view->uri = $this->_request->getPathInfo();
		$this->layout()->setVariable('active_sub', 'None');
		$this->layout()->setVariable('title', FALSE);
		
		$type = $this->params()->fromRoute('type');
		$this->layout()->setVariable('type', $type); 
		$page = $this->params()->fromRoute('page');
		$this->layout()->setVariable('page', $page); 
		
		return $e; 
		  
	}
	
    
    public function indexAction()
    {
    	return $this->ajaxOutput(array());
    }
    
    public function projectsAction()
    { 
    	return $this->ajaxOutput(array());
    }
    
    public function companiesAction()
    {
        return $this->ajaxOutput();
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