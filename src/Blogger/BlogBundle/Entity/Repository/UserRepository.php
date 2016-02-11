<?php

namespace Blogger\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param $currentUser
     * @return array
     */
    public function getUsersWithoutCurrent($currentUser)
    {
        $qb = $this->createQueryBuilder('user')
            ->select('user.username')
            ->where('user.username NOT LIKE :currentUser')
            ->setParameter('currentUser', $currentUser)
            ->getQuery()
            ->getResult();

        $users = [];

        foreach ($qb as $user) {
             $users[$user['username']] = $user['username'];
        }

        return $users;
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        $qb = $this->createQueryBuilder('user')
            ->select('user.username')
            ->getQuery()
            ->getResult();

        $users = [];

        foreach ($qb as $user) {
            $users[$user['username']] = $user['username'];
        }

        return $users;
    }
}