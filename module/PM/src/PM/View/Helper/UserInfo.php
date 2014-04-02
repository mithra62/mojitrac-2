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

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - User Info View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/UserInfo.php
 */
class UserInfo extends BaseViewHelper
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
			$user = $serviceManager->get('Application\Model\Users');
			$this->userInfo = $user->getUserById($id);
		}
		return $this->userInfo;
	}
}