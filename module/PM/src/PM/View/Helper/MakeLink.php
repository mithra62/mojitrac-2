<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/MakeLink.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;
use Application\View\Helper\AbstractViewHelper;

/**
 * PM - Make Link View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/View/Helper/MakeLink.php
 */
class MakeLink extends AbstractViewHelper
{
    public $view;
    
    private $identity;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        $this->identity = Zend_Auth::getInstance()->getIdentity();
    }
    	
	public function MakeLink($type, array $info)
	{
		switch($type)
		{
			case 'user':
				if($this->view->CheckPermission('view_users_data') || $this->identity == $info['id'])
				{
					return $this->createLink($this->makeUserLink($info), $this->makeUserLinkBody($info));
				}
				else
				{
					return $this->makeUserLinkBody($info);
				}
			break;
			
			case 'role':
				if($this->view->CheckPermission('manage_roles'))
				{
					return $this->createLink($this->makeRoleLink($info), $info['name']);
				}
				else
				{
					return $info['name'];
				}				
			break;
			
			case 'project':
				
			break;
			
			case 'task':
				
			break;
			
			case 'back':
				return $this->makeBackLink($info);
			break;
			
		}
	}
	
	private function makeUserLink(array $info)
	{
		return $this->view->url(array('module' => 'pm','controller' => 'users','action'=>'view', 'id' => $info['id']), null, TRUE);
	}

	private function makeRoleLink(array $info)
	{
		return $this->view->url(array('module' => 'pm','controller' => 'roles','action'=>'view', 'id' => $info['id']), null, TRUE);
	}	
	
	private function makeUserLinkBody(array $info)
	{
		if($info['id'] == $this->identity)
		{
			return 'You';
		}
		
		return $info['first_name'].' '.$info['last_name'];
	}
	
	private function createLink($url, $body_part)
	{
		return '<a title="'.$body_part.'" href="'.$url.'">'.$body_part.'</a>';	
	}
}