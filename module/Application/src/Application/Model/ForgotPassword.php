<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/forms/ForgotPassword.php
*/

namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Sql\Sql;

use Application\Model\Users;

/**
* Forgot Password Model
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/models/ForgotPassword.php
*/
class ForgotPassword extends AbstractModel
{
	protected $inputFilter;
	
	public function __construct(\Zend\Db\Adapter\Adapter $db, \Zend\Db\Sql\Sql $sql, \Application\Model\Users $users = null)
	{
		parent::__construct($db, $sql);
		$this->users = $users;
	}

	/**
	 * Sets the Input Filter to use
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	public function getInputFilter()
	{
		if (!$this->inputFilter) 
		{	
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'email',
				'required' => true,
				'filters'  => array(
						array('name' => 'StripTags'),
						array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'EmailAddress',
					),
					array(
						'name' => 'Db\RecordExists',
						'options' => array(
						    'table' => 'users',
						    'field' => 'email',
							'adapter' => $this->adapter
						)
					),
				),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}	
	
	/**
	 * Sends the Forgot Password email
	 * @param \Application\Model\Mail $mail
	 * @param \Application\Model\Hash $hash
	 * @param string $email_address
	 * @return boolean
	 */
	public function sendEmail(\Application\Model\Mail $mail, \Application\Model\Hash $hash, $email_address)
	{
		$guid = $hash->guidish();
		$user_data = $this->users->getUserByEmail($email_address);
		if(!$user_data)
		{
			return FALSE;
		}
		
		if($this->users->upatePasswordHash($user_data['id'], $guid))
		{
			$change_url = $mail->web_url.'/forgot-password/reset/p/'.$guid;
			$mail->addTo($email_address);
			$mail->setViewDir($this->getModulePath(__DIR__));
			$mail->setEmailView('forgot-password', array('change_url' => $change_url, 'user_data' => $user_data));
			$mail->addTo($user_data['email'], $user_data['first_name'].' '.$user_data['last_name']);
			$mail->setSubject('forgot_password_email_subject');
			return $mail->send($mail->transport);	
		}	
	}
	
	private function emailBody($change_url)
	{
		$str = "To reset your password for your MojiTrac account, click the link below:\n\n";
		$str .= $change_url."\n\n";
		$str .= "Copy and paste the URL in a new browser window if you can't click on it.\n\n";
		$str .= "Please keep in mind that the link will only work for 24 hours; after that it will be inactive.\n\n";
		$str .= "If you didn't request to reset your password you don't need to take any further action and can safely disregard this email.\n\n";
		$str .= "MojiTrac :).\n\n";
		$str .= "Please don't respond to this email; all emails are automatically deleted.";
		return $str;
	}
}