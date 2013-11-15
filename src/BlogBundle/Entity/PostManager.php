<?php

namespace BlogBundle\Entity;

use BlogBundle\Entity\Post;
use PDOBundle\Service\Client as PDOClient;
use BlogBundle\ValueObject\PostModifier;

class PostManager
{
    const TABLE = 'Post';

    protected $pdoClient;

    public function __construct(PDOClient $pdoClient)
    {
        $this->pdoClient = $pdoClient;
    }
    
    public function findBlogPosts(Blog $blog)
    {
        return $this->pdoClient->map(
            'SELECT * FROM ' . self::TABLE . ' WHERE blog_id = ? ORDER BY id DESC', array($blog->getId()),
            '\BlogBundle\Entity\Post',
            PDOClient::FETCH_ALL
        );
    }

    public function edit(PostModifier $modifier)
    {
        return $this->pdoClient->update(self::TABLE, $modifier->getForm()->getData(), 'id = ' . $modifier->getPost()->getId());
    }

    public function destroy(Post $post)
    {
         return $this->pdoClient->executeQuery('DELETE FROM ' . self::TABLE . ' WHERE id = ?', array($post->getId()));
    }

    public function findOneById($id)
    {
        if (!is_numeric($id)) {
            return;
        }

        return $this->pdoClient->map(
            'SELECT * FROM ' . self::TABLE . ' WHERE id = ?', array($id),
            '\BlogBundle\Entity\Post',
            PDOClient::FETCH_SINGLE
        );
    }

    public function createPost(Post $post)
    {
        $this->pdoClient->insert(self::TABLE, $post->toArray());
    }
}
