<?php

namespace BlogBundle\Entity;

use AuthBundle\Entity\User;
use PDOBundle\Service\Client as PDOClient;
use BlogBundle\ValueObject\BlogModifier;

class BlogManager
{
    const TABLE = 'Blog';

    protected $pdoClient;

    public function __construct(PDOClient $pdoClient)
    {
        $this->pdoClient = $pdoClient;
    }

    public function findAllByUser(User $user)
    {
        return $this->pdoClient->map(
            'SELECT * FROM ' . self::TABLE . ' WHERE user_id = ? ORDER BY id DESC', array($user->getId()),
            '\BlogBundle\Entity\Blog',
            PDOClient::FETCH_ALL
        );
    }
    
    public function findOneByDomain($domain)
    {
        return $this->pdoClient->map(
            'SELECT * FROM ' . self::TABLE . ' WHERE domain = ?', array($domain),
            '\BlogBundle\Entity\Blog',
            PDOClient::FETCH_SINGLE
        );
    }
    
    public function getLast($limit)
    {
        return $this->pdoClient->map(
            'SELECT * FROM ' . self::TABLE . ' ORDER BY id DESC LIMIT ' . $limit, array(),
            '\BlogBundle\Entity\Blog',
            PDOClient::FETCH_ALL
        );
    }

    public function edit(BlogModifier $modifier)
    {
        return $this->pdoClient->update(self::TABLE, $modifier->getForm()->getData(), 'id = ' . $modifier->getBlog()->getId());
    }

    public function destroy(Blog $blog)
    {
         return $this->pdoClient->executeQuery('DELETE FROM ' . self::TABLE . ' WHERE id = ?', array($blog->getId()));
    }

    public function findOneById($id)
    {
        if (!is_numeric($id)) {
            return;
        }

        return $this->pdoClient->map(
            'SELECT * FROM ' . self::TABLE . ' WHERE id = ?', array($id),
            '\BlogBundle\Entity\Blog',
            PDOClient::FETCH_SINGLE
        );
    }

    public function createBlog(Blog $blog)
    {
        $this->pdoClient->insert(self::TABLE, array(
            'title' => $blog->getTitle(),
            'description' => $blog->getDescription(),
            'user_id' => $blog->getUser()->getId(),
            'domain' => $blog->getDomain()
        ));
    }
}
