<?php
 /**
 * mithra62 - MojiTrac
 *
 * @package		mithra62:Mojitrac
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/PM/src/PM/Model/Ips.php
 */

namespace PM\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

use Application\Model\AbstractModel;

 /**
 * PM - Ip Locker Model
 *
 * @package 	mithra62:Mojitrac
 * @author		Eric Lamb
 * @filesource 	./module/PM/src/PM/Model/Ips.php
 */
class Ips extends AbstractModel
{
    protected $inputFilter;
    
    /**
     * The Companies Model
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
				'name'     => 'ip',
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
    
    /**
     * Returns an array for modifying $_name
     * @param $data
     * @return array
     */
    public function getSQL($data){
    	return array(
    			'name' => $data['name'],
    			'phone1' => $data['phone1'],
    			'phone2' => $data['phone2'],
    			'fax' => $data['fax'],
    			'address1' => $data['address1'],
    			'address2' => $data['address2'],
    			'city' => $data['city'],
    			'state' => $data['state'],
    			'zip' => $data['zip'],
    			'primary_url' => $data['primary_url'],
    			'description' => $data['description'],
    			'type' => $data['type'],
    			'custom' => $data['custom'],
    			'last_modified' => new \Zend\Db\Sql\Expression('NOW()')
    	);
    }
    
	/**
	 * Returns all the Ip Addresses
	 */
	public function getAllIps()
	{
		$sql = $this->db->select()->from('ips');
		return $this->getRows($sql);
	}
	
	/**
	 * Returns the IP for the pk
	 * @param int $id
	 */
	public function getIpById($id)
	{
		$sql = $this->db->select()->from(array('ip'=>'ips'));
		$sql = $sql->join(array('u' => 'users'), 'u.id = ip.creator', array('first_name', 'last_name'), 'left');
		$sql = $sql->where(array('ip.id' => $id));
		return $this->getRow($sql);
	}
	
	/**
	 * Checks if the provided Ip Address is allowed in the system
	 * @param int $ip
	 */
	public function isAllowed($ip)
	{
		$sql = $this->db->select()->from(array('ip' => 'ips'))->columns( array('id'))->where(array('ip' => ip2long($ip)));
		$data = $this->getRow($sql);
		return $data;
	}
	
	/**
	 * Adds an Ip Address to the white list
	 * @param array $data
	 * @param int $creator
	 */
	public function addIp(array $data, $creator)
	{
		if($this->isAllowed($data['ip']))
		{
			return TRUE;
		}
		
		$sql = $this->db->getSQL($data);
		$sql['creator'] = $creator;
		if($this->db->addIp($sql))
		{
		    return TRUE;			
		}	
	}
	
	/**
	 * Removes an Ip Address from the white list
	 * @param string $key
	 * @param stirng $col
	 */
	public function removeIp($key, $col = 'id')
	{
		if($this->db->deleteIp($key, $col))
		{
		    return TRUE;			
		}
	}
	
	/**
	 * Updates an Ip Address on the white list
	 * @param array $data
	 * @param int $id
	 */
	public function updateIp(array $data, $id)
	{
		$sql = $this->db->getSQL($data);
		if($this->db->update($sql, "id = '$id'"))
		{
		    return TRUE;	
		}
	}
}