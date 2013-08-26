<?php
/**
 * mithra62 - MojiTrac
*
* @package		mithra62:Mojitrac
* @author		Eric Lamb
* @copyright	Copyright (c) 2013, mithra62, Eric Lamb.
* @link			http://mithra62.com/
* @version		1.0
* @filesource 	./moji/application/forms/Abstract.php
*/

namespace Application\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
* Form Abstract
*
* Adds the constant form elements
*
* @package 		mithra62:Mojitrac
* @author		Eric Lamb
* @filesource 	./moji/application/forms/Abstract.php
*/
abstract class AbstractForm extends Form
{
	/**
	 * Simple Yes/No translations
	 * @var array
	 */
	public $yn_arr = array('0' => 'No', '1' => 'Yes');
	
	/**
	 * Adds the constant form elements
	 * @param string $options
	 */
	public function __construct($form_name, array $options = array())
	{
		parent::__construct($form_name, $options);
		
		$this->add(array(
			'name' => '_x',
			'type' => 'Csrf',
			'options' => array(
				'csrf_options' => array(
					'timeout' => 600
				)
			)
		));		
	}	
}