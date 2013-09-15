<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/Application/src/Application/Form/PrefsForm.php
*/

namespace Application\Form;

use Base\Form\BaseForm;

/**
* PrefsForm Form
*
* Generates the Preferences form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/Application/src/Application/Form/PrefsForm.php
*/
class PrefsForm extends BaseForm
{
	/**
	 * Returns the Preferences form
	 * @param string $options
	 */	
	public function __construct($options = null) 
	{
		parent::__construct($options);
		
		$this->add(array(
			'name' => 'noti_assigned_task',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '', 
				'id' => 'noti_assigned_task',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));

		$this->add(array(
			'name' => 'noti_status_task',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'noti_status_task',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));

		$this->add(array(
			'name' => 'noti_file_uploaded',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'noti_file_uploaded',
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));

		$this->add(array(
			'name' => 'noti_file_revision_uploaded',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'noti_file_revision_uploaded'
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));	

		$this->add(array(
			'name' => 'noti_remove_proj_team',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'noti_remove_proj_team'
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));	

		$this->add(array(
			'name' => 'noti_add_proj_team',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'noti_add_proj_team'
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));

		$this->add(array(
			'name' => 'noti_daily_task_reminder',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'noti_daily_task_reminder'
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));

		$this->add(array(
			'name' => 'noti_priority_task',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'noti_priority_task'
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));
			
		/*
		$this->add(array(
			'name' => 'timezone',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'timezone'
			),
			'options' => array(
				'value_options' => PM_Model_Options_Timezones::tz(),
			)
		));	

		$this->add(array(
			'name' => 'date_format',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'date_format'
			),
			'options' => array(
				'value_options' => PM_Model_Options_Datetime::date_formats(),
			)
		));	

		$this->add(array(
			'name' => 'date_format_custom',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input small',
				'id' => 'date_format_custom'
			),
		));	
			
		$this->add(array(
			'name' => 'time_format',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'time_format'
			),
			'options' => array(
				'value_options' => PM_Model_Options_Datetime::time_formats(),
			)
		));	

		$this->add(array(
			'name' => 'time_format_custom',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input small',
				'id' => 'time_format_custom'
			),
		));

		*/
		$this->add(array(
			'name' => 'enable_rel_time',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'enable_rel_time'
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
			)
		));

		$this->add(array(
			'name' => 'enable_contextual_help',
			'type' => 'Checkbox',
			'attributes' => array(
				'class' => '',
				'id' => 'enable_contextual_help'
			),
			'options' => array(
				'checked_value' => '1',
				'unchecked_value' => '0'
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