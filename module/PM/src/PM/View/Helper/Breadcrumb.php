<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/Breadcrumb.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Breadcrumb View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View/Helper/Breadcrumb.php
 */
class Breadcrumb extends BaseViewHelper
{
	/**
	 * Contains our nav data 
	 * @var array
	 */
	private $breadcrumb = array();
	
	/**
	 * The amount of items the breadcrumb has
	 * @var int
	 */
	private $key = 0;
	
	/**
	 * The section the main breadcrumb is on
	 * @var string
	 */
	private $type;
	
	/**
	 * The database object
	 * @var object
	 */
	private $db; 
	
	/**
	 * The primary key the main breadcrumb is on; joined with $type
	 * @var int
	 */
	private $pk;
	
	/**
	 * The view object
	 * @var object
	 */
	public $view;
	
	/**
	 * The available breadcrumb types
	 * @var array
	 */
	private $avail_types = array('task', 'project', 'company', 'file');
    
    /**
     * The main method
     * @param string $type
     * @param int $pk
     */
    public function __invoke($type, $pk)
    {
    	if(!in_array($type, $this->avail_types))
    	{
    		echo 'Fail';
    		return;
    	}
    	
    	$this->type = $type;
    	$this->pk = $pk;
    	$this->add_breadcrumb('/pm/', 'Home');
    	switch($this->type)
    	{
    		case 'task':
    			$this->add_task();
    		break;
    		
    		case 'project':
    			$this->add_project();
    		break;

    		case 'company':
    			$this->add_company();
    		break;

    		case 'file':
    			$this->add_file();
    		break;     		
    	}
    	
    	return '<div id="breadcrumbs">'.$this->create_links().'</div>';
    }
    
    private function add_company()
    {
    	$helperPluginManager = $this->getServiceLocator();
    	$serviceManager = $helperPluginManager->getServiceLocator();
    	$company = $serviceManager->get('PM/Model/Companies');
    	
    	$result = $company->getCompanyById($this->pk);	
    	if($result)
    	{
    		$company_url = $this->view->url('companies/view', array('company_id' => $result['id']));
    		$this->add_breadcrumb($company_url, $result['name'], TRUE);
    	}    	
    }
    
    private function add_project()
    {
    	$helperPluginManager = $this->getServiceLocator();
    	$serviceManager = $helperPluginManager->getServiceLocator();
    	$projects = $serviceManager->get('PM/Model/Projects');
    	$result = $projects->getProjectById($this->pk);	
    	
    	if($result)
    	{
    		$company_url = $this->view->url('companies/view', array('company_id' => $result['company_id']));
    		$project_url = $this->view->url('projects/view', array('company_id' => FALSE, 'project_id' => $result['id']));
    		
    		$this->add_breadcrumb($company_url, $result['company_name']);
    		$this->add_breadcrumb($project_url, $result['name'], TRUE);
    	}
    }

    private function add_task()
    {
    	$helperPluginManager = $this->getServiceLocator();
    	$serviceManager = $helperPluginManager->getServiceLocator();
    	 
    	$task = $serviceManager->get('PM/Model/Tasks');

    	$result = $task->getTaskById($this->pk);
    	if($result)
    	{
    		$company_url = $this->view->url('companies/view', array('company_id' => $result['company_id']));
    		$project_url = $this->view->url('projects/view', array('project_id' => $result['project_id']));
    		$task_url = $this->view->url('tasks/view', array('task_id' => $result['id']));
    		
    		$this->add_breadcrumb($company_url, $result['company_name']);
    		$this->add_breadcrumb($project_url, $result['project_name']);
    		$this->add_breadcrumb($task_url, $result['name'], TRUE);
    	}
    } 
    
    private function add_file()
    {
    	$this->db = new PM_Model_DbTable_Files();
		$sql = $this->db->select()->setIntegrityCheck(false)->from(array('f'=>$this->db->getTableName()), array('f.project_id', 'f.task_id', 'f.company_id', 'f.id AS file_id', 'f.name AS file_name'));
		$sql = $sql->where('f.id = ?', $this->pk);
		
		$sql = $sql->joinLeft(array('p' => 'projects'), 'p.id = f.project_id', array('name AS project_name'));
		$sql = $sql->joinLeft(array('t' => 'tasks'), 't.id = f.task_id', array('name AS task_name'));
		$sql = $sql->joinLeft(array('c' => 'companies'), 'c.id = f.company_id', array('name AS company_name'));
    	
    	
    	$result = $this->db->getFile($sql);	
    	if($result)
    	{
    		if($result['company_name'] != '' && $result['company_id'] && $result['company_id'] > 0)
    		{
    			$company_url = $this->view->url(array('module' => 'pm','controller' => 'companies','action'=>'view', 'id' => $result['company_id']), null, TRUE);
    			$this->add_breadcrumb($company_url, $result['company_name']);
    		}
    		
    		if($result['project_name'] != '' && $result['project_id'] && $result['project_id'] > 0)
    		{    		
    			$project_url = $this->view->url(array('module' => 'pm','controller' => 'projects','action'=>'view', 'id' => $result['project_id']), null, TRUE);
    			$this->add_breadcrumb($project_url, $result['project_name']);
    		}
    		
    		if($result['task_name'] != '' && $result['task_id'] && $result['task_id'] > 0)
    		{
    			$task_url = $this->view->url(array('module' => 'pm','controller' => 'tasks','action'=>'view', 'id' => $result['task_id']), null, TRUE);
    			$this->add_breadcrumb($task_url, $result['task_name']);
    		}
    		
    		$file_url = $this->view->url(array('module' => 'pm','controller' => 'files','action'=>'view', 'id' => $result['file_id']), null, TRUE);
    		$this->add_breadcrumb($file_url, 'File: '.$result['file_name'], TRUE);    		
    	}
    }     

    public function create_links()
    {
    	$txt = '<ul>';
    	$last = end($this->breadcrumb);
    	foreach($this->breadcrumb AS $breadcrumb)
    	{
    		$url = '';
    		$class = FALSE;
    		if($breadcrumb['active'] == '1')
    		{
    			$class = 'active';
    		}
    		$txt .= '<li><a href="'.$breadcrumb['url'].'" class="'.$class.'" title="'.$breadcrumb['txt'].'">'.$breadcrumb['txt'].'</a></li>';
    		$class = '';
    	}
    	
    	$txt .= '</ul>';
    	return $txt;
    }

    public function add_breadcrumb($url, $txt, $active = FALSE)
    {
    	$this->breadcrumb[$this->key]['txt'] = $txt;
		$this->breadcrumb[$this->key]['url'] = $url;
		if($active)
		{
			$this->breadcrumb[$this->key]['active'] = '1';
		}
		else
		{
			$this->breadcrumb[$this->key]['active'] = '0';
		}
		$this->key++;
    }    
}