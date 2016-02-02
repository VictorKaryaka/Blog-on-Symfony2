<?php
namespace Blogger\BlogBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Blogger\BlogBundle\Entity\Blog;

class CRUDController extends Controller
{
    /**
     * @param Blog $blog
     * @return JsonResponse
     * @throws \Exception
     */
    public function getCommentsAction(Blog $blog)
    {
        $comments = $this->getDoctrine()->getManager()
            ->getRepository('BloggerBlogBundle:Comment')->getCommentsForBlog($blog);

        $commentsParse = [];

        if (!empty($comments)) {
            foreach ($comments as $comment) {
                $commentsParse[] = [
                    'id' => $comment->getId(),
                    'comment' => $comment->getComment()
                ];
            }
        }

        return new JsonResponse(['comments' => $commentsParse]);
    }
}