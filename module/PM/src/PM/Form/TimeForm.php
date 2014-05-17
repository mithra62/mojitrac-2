<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Forms/TimeForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;

/**
* PM - Time Tracker Form
*
* Returns the Time Tracker form 
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Forms/TimeForm.php
*/
class TimeForm extends BaseForm
{
	/**
	 * Returns the Time Tracker form
	 * @param string $options
	 */
	public function __construct($name = null, array $company_options = array(), array $project_options = array(), array $task_options = array()) 
	{
		parent::__construct($name);
		$this->add(array(
			'name' => 'date',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input medium timetracker',
				'id' => 'date',
				'autocomplete' => 'off',
				'style' => 'min-width:60px;',
			),
		));
		
		$this->add(array(
			'name' => 'hours',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input small timetracker',
				'id' => 'hours',
				'autocomplete' => 'off',
				'style' => 'min-width:30px;',
			),
		));	

		$this->add(array(
			'name' => 'company_id',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input timetracker',
				'id' => 'company_id'
			),
			'options' => array(
				'value_options' => $company_options,
			)
		));	

		$this->add(array(
			'name' => 'task_id',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input timetracker',
				'id' => 'task_id'
			),
			'options' => array(
				'value_options' => $task_options,
			)
		));	

		$this->add(array(
			'name' => 'project_id',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input timetracker',
				'id' => 'project_id'
			),
			'options' => array(
				'value_options' => $project_options,
			)
		));	

		$this->add(array(
			'type' => 'Zend\Form\Element\Text',
			'name' => 'description',
			'attributes' => array(
				'class' => 'input large timetracker',
				'style' => 'min-width:150px;',
				'id' => 'description',
				'rows' => '1'
			),
		));	

		$this->add(array(
			'name' => 'billable',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => 'checkbox', 
				'id' => 'billable',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
	}
}