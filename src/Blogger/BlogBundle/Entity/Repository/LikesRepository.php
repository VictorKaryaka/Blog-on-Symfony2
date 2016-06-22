<?php

namespace Blogger\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class LikesRepository extends EntityRepository
{
    /**
     * @param $blogId
     * @return String
     */
    public function getNumberOfLikes($blogId)
    {
        $qb = $this->createQueryBuilder('likes')
            ->select('COUNT(likes)')
            ->where('likes.blog = :blogId')
            ->setParameter('blogId', $blogId);

        $resultQuery = $qb->getQuery()->getSingleResult();
        $likes = $resultQuery[1];

        return $likes;
    }

    /**
     * @param $blogId
     * @param $userId
     * @return bool
     */
    public function hasLike($blogId, $userId)
    {
        $qb = $this->createQueryBuilder('likes')
            ->select('COUNT(likes)')
            ->where('likes.blog = :blogId')
            ->andWhere('likes.userId = :userId')
            ->setParameter('blogId', $blogId)
            ->setParameter('userId', $userId);

        $result = $qb->getQuery()->getSingleResult()[1];

        return ($result > 0) ? true : false;
    }

    /**
     * @param $blogId
     * @param $userId
     */
    public function deleteLike($blogId, $userId)
    {
        $qb = $this->createQueryBuilder('likes')
            ->select('likes')
            ->where('likes.blog = :blogId')
            ->andWhere('likes.userId = :userId')
            ->setParameter('blogId', $blogId)
            ->setParameter('userId', $userId);

        $result = $qb->getQuery()->getSingleResult();
        $em = $this->getEntityManager();
        $em->remove($result);
        $em->flush();
    }
}