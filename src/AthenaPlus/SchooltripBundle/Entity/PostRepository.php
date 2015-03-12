<?php

namespace AthenaPlus\SchooltripBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * PostRepository
 *
 *
 */
class PostRepository extends EntityRepository
{
    public function findLast($number = 10, $offset = 0)
    {
        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM SchooltripBundle:Post p ORDER BY p.created DESC'
            )
            ->setMaxResults($number)
            ->setFirstResult($offset)
            ->getResult();
        $posts = array();
        foreach ($result as $post){
            $newPost = array();
            $newPost['id'] = $post->getId();
            $newPost['created'] = $post->getCreated();
            $newPost['created_by'] = $post->getCreatedBy() ? $post->getCreatedBy()->getUsername() : "-";
            $newPost['text'] = $post->getText();
            $posts[] = $newPost;
        }

        return array_reverse($posts);
    }
}
