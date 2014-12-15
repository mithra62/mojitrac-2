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
use HostManager\Traits\Account;

/**
 * HostManager - Accounts Model
 *
 * @package 	HostManager
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Model/Accounts.php
 */
class Accounts extends AbstractModel
{
	use Account;
	
	/**
	 * Prepares the SQL array for the accounts table
	 * @param array $data
	 * @return array
	 */	
	public function getSQL(array $data){
		return array(
			'slug' => $data['subdomain'],
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
				'name'     => 'organization',
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
	 * Returns the data on an account
	 * @param array $where
	 * @return Ambigous <\Base\Model\array:, multitype:, unknown, \Zend\EventManager\mixed, NULL, mixed>
	 */
	public function getAccount(array $where = array())
	{
		$sql = $this->db->select()->from(array('a'=> 'accounts'));
		if( $where )
		{
			$sql = $sql->where($where);
		}
		
		return $this->getRow($sql);	
	}
	
	/**
	 * Creates a MojiTrac account 
	 * @param array $data
	 * @param \Application\Model\Users $user
	 * @param \PM\Model\Companies $company
	 * @param \Application\Model\Hash $hash
	 * @param \Application\Model\Settings $setting
	 * @param \PM\Model\Options $hash
	 */
	public function createAccount(array $data, 
			\Application\Model\Users $user, 
			\PM\Model\Companies $company, 
			\Application\Model\Hash $hash, 
			\Application\Model\Settings $setting, 
			\PM\Model\Options $option
	)
	{		
		$user_data = $user->getUserByEmail($data['email']);
		if( !$user_data )
		{
			$user_id = $user->addUser($data, $hash);
			$user_data = $user->getUserById($user_id);
		}
		
		$sql = $this->getSQL($data);
		$sql['owner_id'] = $user_data['id'];
		$sql['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$account_id = $this->insert('accounts', $sql);
		
		$this->linkUserToAccount($user_data['id'], $account_id);
		
		//create the user roles now
		$user_roles = $user->roles->getAllRoles(array('account_id' => 1));
		$new_user_roles = array();
		foreach($user_roles AS $user_role)
		{
			$permissions = $user->roles->getRolePermissions($user_role['id']);
			$sql = array('name' => $user_role['name'], 'description' => $user_role['description'], 'account_id' => $account_id, 'created_date' => new \Zend\Db\Sql\Expression('NOW()'), 'last_modified' => new \Zend\Db\Sql\Expression('NOW()'));
			$role_id = $this->insert('user_roles', $sql);
			foreach($permissions As $perm)
			{
				$sql = array('role_id' => $role_id, 'permission_id' => $perm);
				$this->insert('user_role_2_permissions', $sql);
			}

			//attach the user to the role
			$sql = array('role_id' => $role_id, 'user_id' => $user_data['id'], 'account_id' => $account_id);
			$this->insert('user2role', $sql);
		}
		
		//now create the initial company
		$company_data = array('name' => $data['organization'], 'type' => '6');
		$company_id = $company->addCompany($company_data);
		if( $company_id )
		{
			$sql = array('account_id' => $account_id);
			$this->update('companies', $sql, array('id' => $company_id));
		}
		
		//and link the new company as the master company for this account
		$setting->updateSettings(array('master_company' => $company_id));
		
		//now create the option types
		$option_data = $option->getAllOptions();
		foreach($option_data AS $opt)
		{
			$sql = array('name' => $opt['name'], 'area' => $opt['area'], 'account_id' => $account_id, 'created_date' => new \Zend\Db\Sql\Expression('NOW()'), 'last_modified' => new \Zend\Db\Sql\Expression('NOW()'));
			$this->insert('options', $sql);
		}		
		
		//and wrap it up so we can go home
		return $account_id;
	}
	
	/**
	 * Links a user to a given account
	 * @param int $user_id
	 * @param int $account_id
	 */
	public function linkUserToAccount($user_id, $account_id)
	{
		$data = array('user_id' => $user_id, 'account_id' => $account_id);
		if( !$this->userOnAccount($user_id, $account_id) )
		{
			$this->insert('user_accounts', $data);
		}
	}
	
	/**
	 * Checks if a user is on a given account
	 * @param int $user_id
	 * @param int $account_id
	 */
	public function userOnAccount($user_id, $account_id)
	{
		$where = array('user_id' => $user_id, 'account_id' => $account_id);
		$sql = $this->db->select()->from('user_accounts')->where($where);
		return $this->getRow($sql);
	}
}