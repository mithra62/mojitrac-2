<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/UserInfo.php
 */

namespace PM\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\Auth\AuthAdapter;
use Application\View\Helper\AbstractViewHelper;

 /**
 * PM - User Info View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/UserInfo.php
 */
class UserInfo extends AbstractViewHelper
{
	public $userInfo = FALSE;
	
	public function __invoke($id, $all = false)
	{
		return $this->getUserInfo($id);
	}
	
	public function getUserInfo($id)
	{
		if(!$this->userInfo)
		{
			$helperPluginManager = $this->getServiceLocator();
			$serviceManager = $helperPluginManager->getServiceLocator();
			$user = $serviceManager->get('Application\Model\User');
			$this->userInfo = $user->getUserById($id);
		}
		return $this->userInfo;
	}
}