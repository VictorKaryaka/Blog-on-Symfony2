<?php

namespace Blogger\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;

class BlogRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getLatestBlogs()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b, c')
            ->leftJoin('b.comments', 'c')
            ->addOrderBy('b.created', 'DESC');

        return $qb;
    }

    /**
     * @param $limit
     * @param $startPage
     * @return array
     */
    public function getPaginationBlogs($limit, $startPage)
    {
        $qb = $this->createQueryBuilder('blog')
            ->where('blog.id >= :startPage')
            ->orderBy('blog.id', 'DESC')
            ->setParameter('startPage', $startPage)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getTags()
    {
        $blogTags = $this->createQueryBuilder('b')
            ->select('b.tags')
            ->getQuery()
            ->getResult();

        $tags = array();

        foreach ($blogTags as $blogTag) {
            $tags = array_merge(explode(",", $blogTag['tags']), $tags);
        }

        foreach ($tags as &$tag) {
            $tag = trim($tag);
        }

        return $tags;
    }

    /**
     * Return weight of tags
     *
     * @param $tags
     * @return array
     */
    public function getTagWeights($tags)
    {
        $tagWeights = array();

        if (empty($tags)) {
            return $tagWeights;
        }

        foreach ($tags as $tag) {
            $tagWeights[$tag] = (isset($tagWeights[$tag])) ? $tagWeights[$tag] + 1 : 1;
        }

        uksort($tagWeights, function () {
            return rand() > rand();
        });

        $max = max($tagWeights);
        $multiplier = ($max > 5) ? 5 / $max : 1;

        foreach ($tagWeights as &$tag) {
            $tag = ceil($tag * $multiplier);
        }

        return $tagWeights;
    }

    /**
     * @param null $tag
     * @return array
     */
    public function getBlogsByTag($tag = null)
    {
        $qb = $this->createQueryBuilder('blog')
            ->select()
            ->where('blog.tags LIKE :tag')
            ->setParameter('tag', '%'.$tag.'%');

        return $qb;
    }
}
