<?php

namespace AuthBundle\Entity;

use PDOBundle\Service\Client as PDOClient;

class UserManager
{
    const TABLE = 'User';
    
    protected $pdoClient;
    
    public function __construct(PDOClient $pdoClient)
    {
        $this->pdoClient = $pdoClient;
    }

    public function registerUser(User $user)
    {
        $this->pdoClient->insert(self::TABLE, array(
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'password' => $user->getPassword(),
            'createdAt' => $user->getCreatedAt()->format('Y-m-d G:i:s')
        ));
    }
    
    public function findOneById($id)
    {
        $user = $this->pdoClient->map(
            'SELECT * FROM ' . self::TABLE . ' WHERE id = ?', array($id),
            'AuthBundle\Entity\User',
            PDOClient::FETCH_SINGLE
        );
        
        return $user;
    }
    
    public function findOneByEmail($email)
    {
        $user = $this->pdoClient->map(
            'SELECT * FROM ' . self::TABLE . ' WHERE email = ?', array($email),
            'AuthBundle\Entity\User',
            PDOClient::FETCH_SINGLE
        );
        
        return $user;
    }
}