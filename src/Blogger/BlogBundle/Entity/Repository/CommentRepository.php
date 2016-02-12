<?php

namespace Blogger\BlogBundle\Entity\Repository;

use Blogger\BlogBundle\Entity\Blog;
use Doctrine\ORM\EntityRepository;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends EntityRepository
{
    /**
     * @param $blogId
     * @param bool|true $approved
     * @return array
     */
    public function getCommentsForBlog($blogId, $approved = true)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.blog = :blog_id')
            ->addOrderBy('c.created')
            ->setParameter('blog_id', $blogId);

        if (false === is_null($approved))
            $qb->andWhere('c.approved = :approved')->setParameter('approved', $approved);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getLatestComments($limit = 10)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->addOrderBy('c.id', 'DESC');

        if (false === is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getCommentsForFirstBlog()
    {
        $qb = $this->getEntityManager()->getRepository('BloggerBlogBundle:Blog')->createQueryBuilder('blog')
            ->select('blog.id')
            ->orderBy('blog.id')->setMaxResults(1);

        $blog = $qb->getQuery()->getSingleScalarResult();

        $comments = $this->createQueryBuilder('comment')
            ->select('comment.id', 'comment.comment')
            ->where('comment.blog = :blogId')
            ->setParameter('blogId', $blog);

        return $comments->getQuery()->getResult();
    }

    /**
     * @param $blogId
     * @return array|bool
     */
    public function getSortComments($blogId)
    {
        $comments = $this->getCommentsForBlog($blogId);

        if (empty($comments)) {
            return false;
        }

        $sortComments = [];
        $idKeyComments = [];
        $parentIdKeyComments = [];

        foreach ($comments as $comment) {
            $idKeyComments[$comment->getId()] = $comment;

            if ($comment->getParentId() != null) {
                $parentIdKeyComments[$comment->getParentId()] = $comment;
            }
        }

        foreach ($idKeyComments as $key => $commentEntity) {
            if (!array_key_exists($key, $idKeyComments)) {
                continue;
            }

            if (!array_key_exists($key, $parentIdKeyComments) && $commentEntity->getParentId() == null) {
                $sortComments[] = $idKeyComments[$key];
            } else {
                $keyNestedComment = $key;

                foreach ($parentIdKeyComments as $id => $entity) {
                    if ($id == $keyNestedComment) {
                        $sortComments[] = $idKeyComments[$keyNestedComment];
                        unset($idKeyComments[$keyNestedComment]);
                        $keyNestedComment = $entity->getId();
                    }
                }

                $sortComments[] = $idKeyComments[$keyNestedComment];
                unset($idKeyComments[$keyNestedComment]);
            }
        }

        return $sortComments;
    }
}
