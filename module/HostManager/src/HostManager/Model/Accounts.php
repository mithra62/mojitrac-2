<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Accounts.php
 */

namespace HostManager\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;

/**
 * HostManager - Accounts Model
 *
 * @package 	HostManager
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Model/Accounts.php
 */
class Accounts extends AbstractModel
{
	/**
	 * Prepares the SQL array for the accounts table
	 * @param array $data
	 * @return array
	 */	
	public function getSQL(array $data){
		return array(
			'name' => $data['name'],
			'area' => $data['area'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}
	
	/**
	 * @ignore
	 * @param InputFilterInterface $inputFilter
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	/**
	 * Returns an instance of the InputFilter for data validation
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'email',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'EmailAddress',
					),
					array(
						'name' => 'Db\NoRecordExists',
						'options' => array(
							'table' => 'users',
						    'field' => 'email',
							'adapter' => $this->adapter
						)
					),
				),
			)));
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'subdomain',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name' => 'Db\NoRecordExists',
						'options' => array(
							'table' => 'accounts',
						    'field' => 'slug',
							'adapter' => $this->adapter
						)
					),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'password',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'last_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'first_name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				)
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Returns the Account ID
	 * @param array $where
	 * @return int
	 */
	public function getAccountId(array $where = array())
	{
		$sql = $this->db->select()->from(array('a'=> 'accounts'))->columns(array('id'))->where($where);
		$account = $this->getRow($sql);
		if( !empty($account['id']) )
		{
			return $account['id'];
		}
	}
	
	
}