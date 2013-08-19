<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/forms/LoginForm.php
*/

namespace Application\Form;

use Zend\Form\Form;

/**
* LoginForm Form
*
* Generates the LoginForm form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/forms/LoginForm.php
*/
class LoginForm extends Form
{
	/**
	 * Generates the LoginForm form
	 * @param string $options
	 */
	public function __construct($name = null)
	{

		// we want to ignore the name passed
		parent::__construct($name);
		$this->setAttribute('method', 'post');
		$this->add(array(
				'name' => 'email',
				'type' => 'Text',
				'attributes' => array(
					'class' => 'input large',
				),
		));
		$this->add(array(
				'name' => 'password',
				'type' => 'Password',
				'attributes' => array(
					'class' => 'input large',
				),
		));
		$this->add(array(
				'name' => '_x',
				'type' => 'Csrf',
				'options' => array(
						'csrf_options' => array(
								'timeout' => 600
						)
				)
		));		
		$this->add(array(
				'name' => 'submit',
				'type' => 'Submit',
				'attributes' => array(
						'value' => 'Go',
						'id' => 'submitbutton',
				),
		));
    }
}