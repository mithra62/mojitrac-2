<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link		http://mithra62.com/
* @version		2.0
* @filesource 	./moji/application/controllers/LoginController.php
*/

namespace Application\Model\Auth;

use Zend\Authentication\Storage;

class AuthStorage extends Storage\Session
{
	public function setRememberMe($rememberMe = 0, $time = 1209600)
	{
		if ($rememberMe == 1) {
			$this->session->getManager()->rememberMe($time);
		}
	}

	public function forgetMe()
	{
		$this->session->getManager()->forgetMe();
	}
}