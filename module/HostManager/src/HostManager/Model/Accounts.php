<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
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
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/HostManager/src/HostManager/Model/Accounts.php
 */
class Accounts extends AbstractModel
{	
	/**
	 * The System Options
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 * @param \Zend\Db\Sql\Sql $db
	 */
	public function __construct(\Zend\Db\Adapter\Adapter $adapter, \Zend\Db\Sql\Sql $db)
	{
		parent::__construct($adapter, $db);
	}
	
	public function getSQL(array $data){
		return array(
			'name' => $data['name'],
			'area' => $data['area'],
			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
		);
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	public function getInputFilter($translator)
	{
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory = new InputFactory();
	
			$inputFilter->add($factory->createInput(array(
				'name'     => 'name',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
                'validators' => array(
                    array(
                      'name' =>'NotEmpty', 
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $translator('required', 'pm') 
                            ),
                        ),
                    ),
                ),
			)));
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Returns all the options for the project types
	 */
	public function getAllProjectTypes()
	{		
			$sql = $this->db->select()->from('options')->columns(array('id', 'name'))
									  ->where(array('area' => $this->areas['project_type']))
									  ->order('name ASC');
			$types = $this->getRows($sql);
		return $types;
	}
	
	/**
	 * Returns all the options for the project types
	 */
	public function getAllTaskTypes()
	{	
		$sql = $this->db->select()->from('options')->columns(array('id', 'name'))
								  ->where(array('area' => $this->areas['task_type']))
								  ->order('name ASC');
		$types = $this->getRows($sql);
		return $types;
	}
	
	public function getOptions($key = FALSE, $value = FALSE, $area)
	{
		$sql = $this->db->select();
		return $this->db->getOptions($sql);		
	}	
	
	/**
	 * Returns all the Options
	 */
	public function getAllOptions()
	{
		$sql = $this->db->select()->from('options');
		return $this->getRows($sql);
	}
	
	public function getOptionById($id)
	{
		$sql = $this->db->select()->from(array('o'=>'options'));
		$sql = $sql->where(array('o.id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Adds an Option to the db
	 * @param array $data
	 * @param int $creator
	 */
	public function addOption(array $data, $creator)
	{
		$sql = $this->getSQL($data);
		$sql['creator'] = $creator;
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		return $this->insert('options', $sql);
	}
	
	/**
	 * Removes an option and updates all the entries for that option
	 * @param string $id
	 */
	public function removeOption($id)
	{
		if($this->remove('options', array('id' => $id)))
		{
			return TRUE;
		}
	}
	
	/**
	 * Updates an Ip Address on the white list
	 * @param array $data
	 * @param int $id
	 */
	public function updateOption(array $data, $id)
	{
		$sql = $this->getSQL($data);
		return $this->update('options', $sql, array('id' => $id));
	}
}