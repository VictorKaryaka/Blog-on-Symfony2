<?php

namespace Blogger\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class ImageRepository extends EntityRepository
{
    /**
     * @param null $blog
     * @param $name
     * @return array
     */
    public function getImageByName($blog = null, $name)
    {
        if ($blog) {
            $this->resetMainImage($blog);
        }

        return $this->createQueryBuilder('image')
            ->select()
            ->where('image.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $blog
     */
    private function resetMainImage($blog)
    {
        $qb = $this->createQueryBuilder('image')
            ->update('')
            ->set('image.main', '0')
            ->where('image.blog = :blog')
            ->setParameter('blog', $blog)
            ->getQuery();

        $qb->execute();
    }
}