<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		2.0
 * @filesource 	./module/HostManager/src/HostManager/Model/Invites.php
 */

namespace HostManager\Model\Account;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Application\Model\AbstractModel;
use HostManager\Traits\Account;

/**
 * HostManager - Invites Model
 *
 * @package 	HostManager
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/HostManager/src/HostManager/Model/Invites.php
 */
class Invites extends AbstractModel
{
	const EventAddAccountInvitePre = 'account.invite.pre';
	const EventAddAccountInvitePost = 'account.invite.post';
	
	/**
	 * Setup the Account Trait
	 */
	use Account;

	/**
	 * Prepares the SQL array for the accounts table
	 * @param array $data
	 * @return array
	 */
	public function getSQL(array $data){
		return array(
			'user_id' => $data['user_id'],
			'account_id' => $data['account_id'],
			'verification_hash' => $data['verification_hash'],
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
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	/**
	 * Creates an Invite to Account in the system
	 * @param int $user_id
	 * @param \Application\Model\Hash $hash
	 * @param string $account_id
	 */
	public function addInvite($user_id, \Application\Model\Hash $hash, $account_id = false)
	{
		if(!$account_id)
		{
			$account_id = $this->getAccountId();
		}
	
		$data = array('user_id' => $user_id, 'account_id' => $account_id, 'verification_hash' => $hash->guidish());

		$ext = $this->trigger(self::EventAddAccountInvitePre, $this, compact('data'), array());
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $data = $ext->last();
				
		$data = $this->getSQL($data);
		$data['created_date'] = new \Zend\Db\Sql\Expression('NOW()');
		$invite_id = $this->insert('account_invites', $data);
		
		$ext = $this->trigger(self::EventAddAccountInvitePost, $this, compact('invite_id'), array());
		if($ext->stopped()) return $ext->last(); elseif($ext->last()) $invite_id = $ext->last();
		
		return $invite_id;
	}
}