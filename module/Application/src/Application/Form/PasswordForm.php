<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Application/src/Application/Form/PasswordForm.php
*/

namespace Application\Form;

use Base\Form\BaseForm;

/**
* Password Form
*
* Generates the Change Password form
*
* @package 		Users
* @author		Eric Lamb
* @filesource 	./module/Application/src/Application/Form/PasswordForm.php
*/
class PasswordForm extends BaseForm
{
	/**
	 * Returns the Password form
	 * @param string $options
	 */	
	public function __construct($name = 'password', $confirm = TRUE) 
	{
		parent::__construct($name);		

		$this->add(array(
			'name' => 'new_password',
			'type' => 'Password',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'new_password'
			),
		));
		
		$this->add(array(
			'name' => 'confirm_password',
			'type' => 'Password',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'confirm_password'
			),
		));	
	}
	
	public function confirmField()
	{
		$this->add(array(
			'name' => 'old_password',
			'type' => 'Password',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'old_password'
			),
		));
		return $this;
	}
}