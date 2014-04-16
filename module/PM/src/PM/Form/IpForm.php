<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		2.0
* @filesource 	./module/PM/src/PM/Forms/IpForm.php
*/

namespace PM\Form;

use Base\Form\BaseForm;

/**
* Ip Locker Form
*
* Generates the Ip Locker form
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./module/PM/src/PM/Forms/IpForm.php
*/
class IpForm extends BaseForm
{
	/**
	 * Returns the Ip Locker form
	 * @param string $options
	 */	
	public function __construct($name) 
	{	
		parent::__construct($name);
		$this->add(array(
			'name' => 'ip',
			'type' => 'Text',
			'attributes' => array(
				'class' => 'input large',
				'id' => 'ip'
			),
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
}