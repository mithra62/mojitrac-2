<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/ActionBlock.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - Action Block View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/ActionBlock.php
 */
class ActionBlock extends BaseViewHelper
{
    
    public function __invoke($icon, $copy, $url, $rel = false)
    {
    	if($icon == 'help')
    	{
    		//$prefs = Zend_Registry::get('pm_prefs');
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