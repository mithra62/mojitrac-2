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
use PM\Model\Options\Files;

/**
* PM - Ip Form
*
* Returns the form for the Option system
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Forms/OptionForm.php
*/
class FileForm extends BaseForm
{
	/**
	 * Returns the File form
	 * @param string $options
	 */	
	public function __construct($name = null, \PM\Model\Files $file) 
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

		$this->add(array(
			'name' => 'status',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
			),
			'options' => array(
				'value_options' => Files::status(),
			)
		));	

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'description',
            'attributes' => array(
                'class' => 'styled_textarea', 
                'rows' => '7',
                'cols' => '40',
            ),
        ));	
	}
	
	public function addFileField()
	{
/**
			$file = new Zend_Form_Element_File('file');
			$file->setRequired(true)
			->setDestination($f->getStoragePath())
			->addValidator('Size', false, $f->getMaxFileSize()) // limit to 100K
			->addValidator('Extension', false, $f->getAllowedExtensions())
			->setMaxFileSize($f->getMaxFileSize()) //limits the filesize on the client side
			->addValidator('Count', false, 1)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->removeDecorator('label')
			->removeDecorator('htmlTag')
			->removeDecorator('description')
			->setAttrib('class', 'input large');	
			**/
		$this->add(array(
			'name' => 'file_upload',
			'type' => 'File',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'file_upload'
			),
		));	
	}
}