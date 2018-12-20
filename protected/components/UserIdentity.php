<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 *
 * @package application.components
 */
class UserIdentity extends CUserIdentity
{
    const ERROR_BLOCK = 3;

    private $_id;

    /**
     * Authenticates a user.
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        /** @var User $UserModel */
        $UserModel = User::model()->find('LOWER(login) = :login', array(':login' => mb_strtolower($this->username,'UTF-8')));
        if (null === $UserModel)
            return $this->errorCode = self::ERROR_USERNAME_INVALID;
        elseif($UserModel->is_blocked)
            return $this->errorCode = self::ERROR_BLOCK;
        else {
            $this->_id = $UserModel->id;
            return $this->errorCode = self::ERROR_NONE;
        }
    }

    /**
     * @return integer the ID of the user record
     */
    public function getId()
    {
        return $this->_id;
    }
}