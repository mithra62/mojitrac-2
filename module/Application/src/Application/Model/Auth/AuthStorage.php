<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./modules/Application/src/Application/Model/Auth/AuthStorage.php
 */

namespace Application\Model\Auth;

use Zend\Authentication\Storage;

/**
 * Application - Authentication Storage
 * 
 * Customizes the storage object so we can do the remember me logic
 *
 * @package 	User\Authentication
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./modules/Application/src/Application/Model/Auth/AuthStorage.php
 */
class AuthStorage extends Storage\Session
{
	/**
	 * Sets the logged in timeout
	 * @param number $rememberMe
	 * @param number $time
	 * @return void
	 */
	public function setRememberMe($rememberMe = 0, $time = 1209600)
	{
		if ($rememberMe == 1) {
			$this->session->getManager()->rememberMe($time);
		}
	}

	/**
	 * Clears up the session so things are all logged out
	 * @return void
	 */
	public function forgetMe()
	{
		$this->session->getManager()->forgetMe();
	}
}