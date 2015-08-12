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

    /**
     * Find the last $number posts from $offset
     * @param int $number
     * @param int $offset
     * @return array
     */
    public function findLast ($number = 10, $offset = 0) {
        $result = $this->getEntityManager()
            ->createQuery('SELECT p FROM SchooltripBundle:Post p ORDER BY p.created DESC')
            ->setMaxResults($number)
            ->setFirstResult($offset)
            ->getResult();
        return $this->convert_result_to_post_list($result);
    }

    /**
     * Convert the result of a query in the Post table to a list acceptable to the display functions in
     * CommunityController. Accepts a query-result and returns an array in the reversed order.
     * @param $result
     * @return array
     */
    protected function convert_result_to_post_list($result) {
        $posts = array ();
        foreach ($result as $post) {
            $new_post = array();
            $new_post['id'] = $post->getId();
            $new_post['created'] = $post->getCreated();
            if ($post->getCreatedBy()) {
                $new_post['created_by'] = $post->getCreatedBy()->getUsername();
            } else {
                $new_post['created_by'] = '-';
            }
            $new_post['text'] = $post->getText();
            array_push($posts, $new_post);
        }
        return array_reverse($posts);
    }
}
