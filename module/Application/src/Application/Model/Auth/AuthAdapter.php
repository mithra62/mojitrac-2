<?php
namespace Application\Model\Auth;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result as AuthenticationResult;

use Application\Model\User;

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
    public function __construct(User $users)
    {
    	$this->users = $users;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\AuthAdapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        //return $authResult = new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID, 1);
        $authMessages = array();
		$data = $this->users->verifyCredentials($this->getIdentity(), $this->getCredential());
		if(!$data)
		{
			$authResult = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
			$authMessages[] = 'Not Found';
        	return new AuthenticationResult( $authResult, $this->email, $authMessages );
		}		
		
        $authResult = AuthenticationResult::SUCCESS;
        return new AuthenticationResult( $authResult, $data['id'], $authMessages );
    }
}