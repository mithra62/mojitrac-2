<?php
class Zend_View_Helper_ActionBlock
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
    
    public function ActionBlock($icon, $copy, $url, $rel = false)
    {
    	if($icon == 'help')
    	{
    		$prefs = Zend_Registry::get('pm_prefs');
    		if(isset($prefs['enable_contextual_help']) && $prefs['enable_contextual_help'] == '0')
    		{
    			return;
    		}
    	}
    	
    	$str = '<div class="actions">';
    	if($rel)
    	{
    		$rel = 'rel = "'.$rel.'"';
    	}
    	$str .= '<a href="'.$url.'" '.$rel.' title="'.$copy.'">';
		$str .= $this->view->InteractIcon($icon, $copy);
		$str .= '<div class="action_text">'.$copy.'</div>';
		$str .= '</a>';
		$str .= '</div>';
		return $str;
    }
    
}