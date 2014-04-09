<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Application/src/Application/Form/RolesForm.php
*/

namespace Application\Form;

use Base\Form\BaseForm;

/**
* Roles Form
*
* Generates the Roles form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/Application/src/Application/Form/RolesForm.php
*/
class RolesForm extends BaseForm
{	
	/**
	 * Returns the User Roles form
	 * @param string $options
	 */	
	public function __construct($name, \Application\Model\Roles $roles)
	{
		parent::__construct($name);
		
		$this->setAttribute('method', 'post');
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'name'
			),
		));	
		
		$this->add(array(
			'type' => 'Textarea',
			'name' => 'description',
			'attributes' => array(
				'class' => 'styled_textarea',
				'rows' => '7',
				'cols' => '40',
			),
		));	
        
		$permissions = $roles->getAllPermissions();
		
		foreach($permissions AS $perm)
		{
		     $this->add(array(
	             'type' => 'Zend\Form\Element\Checkbox',
	             'name' => $perm['name'],
	             'options' => array(
                     'use_hidden_element' => true,
                     'checked_value' => 'yes',
                     'unchecked_value' => 'no',
	             	'id' => $perm['name']
	             )
		     ));
		}
    }
}