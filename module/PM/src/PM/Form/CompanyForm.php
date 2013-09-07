<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Forms/CompanyForm.php
*/

namespace PM\Form;

use PM\Model\Options\Us\States;
use PM\Model\Options\Companies;
use Base\Form\BaseForm;

/**
* Compnany Form
*
* Generates the Company form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Forms/CompanyForm.php
*/
class CompanyForm extends BaseForm
{
	/**
	 * Returns the Company form
	 * @param string $options
	 */	
	public function __construct($name) 
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
			'name' => 'phone1',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone1'
			),
		));

		$this->add(array(
			'name' => 'phone2',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'phone2'
			),
		));

		$this->add(array(
			'name' => 'fax',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'fax'
			),
		));

		$this->add(array(
			'name' => 'address1',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'address1'
			),
		));


		$this->add(array(
			'name' => 'address2',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'address2'
			),
		));


		$this->add(array(
			'name' => 'city',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'city'
			),
		));
		
		$this->add(array(
			'name' => 'state',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'state'
			),
			'options' => array(
				'value_options' => States::states(TRUE),
			)
		));	

		$this->add(array(
			'name' => 'zip',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'zip'
			),
		));

		$this->add(array(
			'name' => 'primary_url',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'primary_url'
			),
		));

		$this->add(array(
			'name' => 'type',
			'type' => 'Select',
			'attributes' => array(
				'class' => 'select input',
				'id' => 'type'
			),
			'options' => array(
				'value_options' => Companies::types(),
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