<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/controllers/ImportController.php
*/

/**
 * Include the Abstract library
 */
include_once 'Abstract.php';

/**
* PM - Import Controller
*
* Routes the Import requests
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/controllers/ImportController.php
*/
class Pm_ImportController extends PM_Abstract
{
	/**
	 * Class preDispatch
	 */
	public function preDispatch()
	{
        parent::preDispatch();
        parent::check_permission('view_companies');
        $this->view->headTitle('Import External Data', 'PREPEND'); 
        $this->view->layout_style = 'single';
        $this->view->sidebar = 'dashboard';
        $this->view->sub_menu = 'admin';
        $this->view->active_nav = 'admin';
		$this->view->active_sub = 'import';
        $this->view->uri = $this->_request->getPathInfo();
		$this->view->title = FALSE;          
	}
    
    public function indexAction()
    {

    }
    
    public function harvestTimeAction()
    {
		$harvest = new PM_Model_Import_Harvest;
		$harvest->importTimesFile($_SERVER['DOCUMENT_ROOT'].'/harvest_all_entries.txt');
    	exit;
    }
    
    public function harvestAction()
    {  	
    	$harvest = new PM_Model_Import_Harvest;
    	$form = $harvest->getForm(array(
            'action' => '/pm/import/harvest',
            'method' => 'post',
        ));
		
		if ($this->getRequest()->isPost()) 
		{
    		
    		$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) 
			{
				if($harvest->importAll($formData['harvest_email'], $formData['harvest_password'], $formData['harvest_account']))
				{
			    	$this->_flashMessenger->addMessage('Harvest Data Imported!');
					$this->_helper->redirector('index','import', 'pm');
					exit;					
				}
			}

		}
		
		$this->view->form = $form;
    }
}