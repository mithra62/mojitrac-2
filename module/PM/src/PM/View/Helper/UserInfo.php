<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/UserInfo.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - User Info View Helper
 *
 * @package 	ViewHelpers\Users
 * @author		Eric Lamb <eric@mithra62.com>
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