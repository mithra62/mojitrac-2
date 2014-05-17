<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/modules/pm/forms/Timer.php
*/

/**
* PM - Timer Form
*
* Returns the Timer form 
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/modules/pm/forms/Timer.php
*/
class Pm_Form_Timer extends Form_Abstract
{
	/**
	 * Returns the Timer form
	 * @param string $options
	 */
	public function __construct($options = null) 
	{

		parent::__construct($options);
				
		$billable = new Zend_Form_Element_Checkbox('billable');
		$billable->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->removeDecorator('label')
				->removeDecorator('htmlTag')
				->removeDecorator('description')
				->setAttrib('class', 'checkbox')
				->setValue('1');

		$description = new Zend_Form_Element_Textarea('description');
		$description->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->removeDecorator('label')
				->removeDecorator('htmlTag')
				->removeDecorator('description')
				->setAttrib('rows', '7')
				->setAttrib('cols', '40')
				->setAttrib('class', 'styled_textarea');				

		$submit = new Zend_Form_Element_Submit('submit');

		$this->addElements(
			array(
				 $description,
				 $billable,
				 $submit
			)
		);	
	}
}