<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Application/src/Application/Form/SettingsForm.php
*/

namespace Application\Form;

use Base\Form\BaseForm;
use PM\Model\Options\Companies;

/**
* Password Form
*
* Generates the Password form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/Application/src/Application/Form/SettingsForm.php
*/
class SettingsForm extends BaseForm
{	
	/**
	 * Returns the System Settings form
	 * @param string $options
	 */	
	public function __construct($name, \PM\Model\Companies $companies) 
	{
		parent::__construct($name);

		$this->add(array(
			'name' => 'master_company',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'master_company'
			),
			'options' => array(
				'value_options' => Companies::companies($companies, TRUE),
			)
		));

		$this->add(array(
			'name' => 'enable_ip',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox', 
				'id' => 'enable_ip',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
	}
}