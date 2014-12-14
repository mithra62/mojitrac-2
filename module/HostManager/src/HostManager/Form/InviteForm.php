<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Form/InviteForm.php
*/

namespace HostManager\Form;

use Base\Form\BaseForm;

/**
 * User Invite Form
 *
 * Generates the User Invite Up Form
 *
 * @package 	HostManager\Users
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Form/InviteForm.php
*/
class InviteForm extends BaseForm
{
	/**
	 * 
	 * @param string $name
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
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Go',
				'id' => 'submitbutton',
			),
		));
    }
}