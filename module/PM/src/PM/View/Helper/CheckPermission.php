<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/CheckPermission.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - Check Permission View Helper
 *
 * @package 	ViewHelpers\Users
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/CheckPermission.php
 */
class CheckPermission extends BaseViewHelper
{
	/**
	 * Checks a given permission 
	 * @param unknown $permission
	 */
	public function __invoke($permission)
	{
		return $this->getPermissions()->check($this->getIdentity(), $permission);
	}

	public function getPermissions()
	{
		if (!$this->permissions) {
			$helperPluginManager = $this->getServiceLocator();
			$serviceManager = $helperPluginManager->getServiceLocator();
			$this->permissions = $serviceManager->get('Application\Model\Permissions');
		}
		return $this->permissions;
	}
}