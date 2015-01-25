<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/View/Helper/getUserAccounts.php
 */

namespace HostManager\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * HostManager - Get User Accounts
 * 
 * Returns an array for a user's linked MojiTrac accounts
 *
 * @package 	ViewHelpers\Accounts
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/View/Helper/getUserAccounts.php
 */
class getUserAccounts extends BaseViewHelper
{	
	/**
	 * @ignore
	 */
    public function __invoke($identity)
    {
		$helperPluginManager = $this->getServiceLocator();
		$serviceManager = $helperPluginManager->getServiceLocator();
		$account = $serviceManager->get('HostManager\Model\Accounts');	
    	$accounts = $account->getUserAccounts(array('user_id' => $identity));
    	print_r($accounts);
    	exit;
		return $accounts;
    }
    
}