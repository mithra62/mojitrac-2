<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/View/Helper/AccountUrl.php
 */

namespace HostManager\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * HostManager - Account URL View Helper
 * 
 * Takes an account ID and creates the full URL domain
 *
 * @param	string	The size to convert
 * @package 	ViewHelpers\Accounts
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/View/Helper/AccountUrl.php
 */
class AccountUrl extends BaseViewHelper
{
	/**
	 * @ignore
	 */
    public function __invoke($account_id, $route)
    {
    	
		$helperPluginManager = $this->getServiceLocator();
		$serviceManager = $helperPluginManager->getServiceLocator();
		$account = $serviceManager->get('HostManager\Model\Accounts');	
			
    	return $account->createAccountUrl($account_id).$route;
    }
    
}