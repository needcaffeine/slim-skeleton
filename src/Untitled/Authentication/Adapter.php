<?php

namespace Untitled\Authentication;

use \Zend\Authentication\Adapter\AdapterInterface;
use \Zend\Authentication\Result;

class Adapter implements AdapterInterface
{
	private $email;

	private $password;

	/**
     * Sets username and password for authentication
     *
     * @return void
     */
	public function __construct($email, $password) {

		$this->email = $email;
		$this->password = $password;
	}

	/**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate() {

		global $em;

		$user = $em->getRepository('Untitled\User')->findOneByEmail($this->email);
		if (!$user) {
			return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, array(), array('The email address and password combination cannot be found in our database. Please try again.'));
        }

		if ($user->checkPassword($this->password)) {
			return new Result(Result::SUCCESS, $user, array());
		}

		return new Result(Result::FAILURE_CREDENTIAL_INVALID, array(), array('The email address and password combination cannot be found in our database. Please try again.'));
	}
}
