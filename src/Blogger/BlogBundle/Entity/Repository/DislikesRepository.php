<?php

namespace Blogger\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class DislikesRepository extends EntityRepository
{
    /**
     * @param $blogId
     * @return String
     */
    public function getNumberOfDislikes($blogId)
    {
        $qb = $this->createQueryBuilder('dislikes')
            ->select('COUNT(dislikes)')
            ->where('dislikes.blog = :blogId')
            ->setParameter('blogId', $blogId);

        $resultQuery = $qb->getQuery()->getSingleResult();
        $dislikes = $resultQuery[1];

        return $dislikes;
    }

    /**
     * @param $blogId
     * @param $userId
     * @return bool
     */
    public function hasDislike($blogId, $userId)
    {
        $qb = $this->createQueryBuilder('dislikes')
            ->select('COUNT(dislikes)')
            ->where('dislikes.blog = :blogId')
            ->andWhere('dislikes.userId = :userId')
            ->setParameter('blogId', $blogId)
            ->setParameter('userId', $userId);

        $result = $qb->getQuery()->getSingleResult()[1];

        return ($result > 0) ? true : false;
    }

    /**
     * @param $blogId
     * @param $userId
     */
    public function deleteDislike($blogId, $userId)
    {
        $qb = $this->createQueryBuilder('dislikes')
            ->select('dislikes')
            ->where('dislikes.blog = :blogId')
            ->andWhere('dislikes.userId = :userId')
            ->setParameter('blogId', $blogId)
            ->setParameter('userId', $userId);

        $result = $qb->getQuery()->getSingleResult();
        $em = $this->getEntityManager();
        $em->remove($result);
        $em->flush();
    }
}