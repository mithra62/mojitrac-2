<?php
class Zend_View_Helper_InteractIcon
{
    public $view;
    
    private $return = array('img','url');

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    	
	function InteractIcon($type, $alt, $atts = FALSE, $return = 'img')
	{
		if(!in_array($return, $this->return))
		{
			$return = 'img';
		}
		
		switch($type)
		{
			case 'folder':
				$icon = 'folder.png';
			break;
			case 'user':
				$icon = 'user.png';
			break;
			case 'pencil':
				$icon = 'pencil.png';
			break;
			case 'cross':
				$icon = 'cross.png';
			break;
			case 'email':
				$icon = 'email.png';
			break;
			case 'www':
				$icon = 'www.png';
			break;
			case 'preview':
				$icon = 'preview.gif';
			break;
			case 'save_disk':
				$icon = 'save_disk.png';
			break;						
			case 'view-all':
				$icon = 'view-all.png';
			break;											
			case 'add':
			default:
				$icon = 'add.png';
			break;
			case 'map':
				$icon = 'map.png';
			break;
			case 'left-arrow':
				$icon = 'icons/arrow_left_16.png';
			break;
			case 'right-arrow':
				$icon = 'icons/arrow_right_16.png';
			break;			
			case 'help':
				$icon = 'icons/info_button_16.png';
			break;			
			case 'tick':
				$icon = 'tick.png';
			break;	
			case 'chart':
				$icon = 'icons/chart_16.png';
			break;	
			case 'timer':
				$icon = 'icons/tool_timer_16.png';
			break;							
		}
		
		$url = $this->view->StaticUrl().'/images/'.$icon;
		if($return == 'img')
		{
			return '<img src="'.$url.'" width="16" height="16" class="png_bg" alt="'.$alt.'" title="'.$alt.'" '.$atts.' />';
		}
		
		return $url;
	}
}