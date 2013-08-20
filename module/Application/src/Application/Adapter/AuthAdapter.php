<?php
namespace Application\Adapter;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result as AuthenticationResult;

use PM\Model\Users;

class AuthAdapter extends AbstractAdapter
{
	private $email;
	
	private $password;
	
	private $type;
	
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct(Users $users, $email, $password, $type = 'pm')
    {
    	$this->email = $email;
    	$this->password = $password;
    	$this->type = $type;
    	$this->users = $users;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {

        //return $authResult = new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID, 1);
        $authMessages = array();
        
		$data = $this->users->verifyCredentials($this->email, $this->password);
		if(!$data)
		{
			$authResult = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
		}		
		
        $authResult = AuthenticationResult::SUCCESS;
    }
}