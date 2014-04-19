<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Forms/NoteForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;
use PM\Model\Options\Notes;

/**
* Note Form
*
* Generates the Note form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Forms/NoteForm.php
*/
class NoteForm extends BaseForm
{
	/**
	 * Returns the Note form
	 * @param string $options
	 */	
	public function __construct($name = null) 
	{

		parent::__construct($name);
		
		$this->add(array(
			'name' => 'subject',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'subject'
			),
		));	

		$this->add(array(
			'name' => 'topic',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'topic'
			),
			'options' => array(
				'value_options' => Notes::topics(),
			)
		));	

		$this->add(array(
			'type' => 'Zend\Form\Element\Textarea',
			'name' => 'description',
			'attributes' => array(
				'class' => 'styled_textarea',
				'rows' => '7',
				'cols' => '40',
				'id' => 'description'
			),
		));
				
	}
}