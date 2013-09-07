<?php 
/**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/View/Helper/CheckPermission.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

 /**
 * PM - Check Permission View Helper
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/View/Helper/CheckPermission.php
 */
class CheckPermission extends BaseViewHelper
{
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