<?php
class Zend_View_Helper_BackToLink
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    
	public function BackToLink(array $options)
	{	
		if($this->view->ajax_mode)
		{
			return;
		}
		
		$this->options = $options;
		$return = '<div class="back_link_content">';
		$data = $this->_parseOptions();
		$return .= $this->template($data['id'], $data['name'], $data['controller']);
		$return .= '<div>'.$this->view->InteractIcon('left-arrow', 'Back').'</div>';
		$return .= '</div><br clear="all" />';
		
		return $return;
		//return $return;
	}
	
	private function _parseOptions()
	{
		$return = array();
		if(isset($this->options['name']) && isset($this->options['id']) && isset($this->options['controller'])  && isset($this->options['action']))
		{
			$return['id'] = $this->options['id'];
			$return['name'] = $this->options['name'];
			$return['controller'] = $this->options['controller'];
			$return['action'] = $this->options['action'];
			return $return;
		}
		
		if($this->options['task'])
		{
			$return['name'] = $this->options['task']['name'];
			$return['id'] = $this->options['task']['id'];
			$return['controller'] = 'tasks';
			$return['action'] = 'view';
		}
		elseif($this->options['project'])
		{
			$return['name'] = $this->options['project']['name'];
			$return['id'] = $this->options['project']['id'];	
			$return['controller'] = 'projects';	
			$return['action'] = 'view';	
		}
		elseif($this->options['company'])
		{
			$return['name'] = $this->options['company']['name'];
			$return['id'] = $this->options['company']['id'];
			$return['controller'] = 'companies';
			$return['action'] = 'view';
		}
		elseif($this->options['user'])
		{
			$return['name'] = $this->options['user']['first_name'].' '.$this->options['user']['last_name'];
			$return['id'] = $this->options['user']['id'];
			$return['controller'] = 'users';
			$return['action'] = 'view';
		}		
		return $return;		
	}
    private function template($id, $name, $controller = 'tasks', $action = 'view')
    {
    	$return = '';
		$return .= '<a href="'.$this->view->url(array('module' => 'pm','controller'=>$controller,'action'=>$action, 'id' => $id), null, TRUE).'" title="Back to '.$name.'">';
		$return .= 'Back to '.$name;
		$return .= '</a>';
		return $return;
    }	
}