<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Forms/OptionForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;

/**
* PM - Ip Form
*
* Returns the form for the Option system
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Forms/OptionForm.php
*/
class OptionForm extends BaseForm
{
	/**
	 * Returns the Option system
	 * @param string $options
	 */	
	public function __construct($name = null, \PM\Model\Options $o) 
	{
		parent::__construct($name);
		
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'name'
			),
		));
		
		$arr = array();
		foreach($o->areas AS $key => $value )
		{
			$arr[$key] = ucwords(str_replace('_', ' ', $value));
		}

		$this->add(array(
			'name' => 'area',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => $arr,
			)
		));		
	}
}