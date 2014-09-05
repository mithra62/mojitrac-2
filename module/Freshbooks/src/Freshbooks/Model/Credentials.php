<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/Freshbooks/src/Freshbooks/Model/Credentials.php
 */

namespace Freshbooks\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

 /**
 * Freshbooks - Credentials Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/Freshbooks/src/Freshbooks/Model/Credentials.php
 */
class Credentials extends AbstractModel
{
    protected $inputFilter;
    
    /**
     * The Freshbooks Model
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Zend\Db\Sql\Sql $db
     */
    public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
    {
    	parent::__construct($adapter, $db);
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
    	throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
    	if (!$this->inputFilter) {
    		$inputFilter = new InputFilter();
    		$factory = new InputFactory();
    
    		$inputFilter->add($factory->createInput(array(
				'name'     => 'freshbooks_domain',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => '\Zend\Validator\Hostname',
						'options' => array(
							'allow' => \Zend\Validator\Hostname::ALLOW_IP
						)
					),
				),
    		)));
    
    		$this->inputFilter = $inputFilter;
    	}
    	
    	return $this->inputFilter;
    }
}