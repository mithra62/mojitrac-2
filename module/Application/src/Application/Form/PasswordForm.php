<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Application/src/Application/Form/PasswordForm.php
*/

namespace Application\Form;

/**
* Password Form
*
* Generates the Password form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/Application/src/Application/Form/PasswordForm.php
*/
class PasswordForm extends AbstractForm
{
	/**
	 * Returns the Password form
	 * @param string $options
	 */	
	public function __construct($name = 'password', $confirm = TRUE) 
	{
		parent::__construct($name);		
		if($confirm)
		{
			$this->add(array(
				'name' => 'old_password',
				'type' => 'Password',
				'attributes' => array(
					'class' => 'input large',
					'id' => 'old_password'
				),
			));
		}

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
}