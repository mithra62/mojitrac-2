<?php
class Zend_View_Helper_ProjectPriority
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    	
	function ProjectPriority($priority)
	{
		$return = PM_Model_Options_Projects::translatePriorityId($priority); 
		$return = '<img src="'.$this->view->StaticUrl().'/images/priorities/'.$priority.'.gif" alt="'.$return.'" title="'.$return.'" /> '.$return;
		return $return; 
	}
}