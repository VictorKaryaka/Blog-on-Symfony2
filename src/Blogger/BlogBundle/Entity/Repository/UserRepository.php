<?php

namespace Blogger\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getUsers()
    {
        return $qb = $this->createQueryBuilder('u')
            ->select('u.username')
            ->getQuery()
            ->getResult();
    }
}