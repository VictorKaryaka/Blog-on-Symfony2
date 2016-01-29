<?php
namespace Blogger\BlogBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Blogger\BlogBundle\Entity\Blog;

class CRUDController extends Controller
{
    public function getCommentsAction(Blog $blog)
    {
        $comments = $this->getDoctrine()->getManager()
            ->getRepository('BloggerBlogBundle:Comment')->getCommentsForBlog($blog);
        $commentsParse = [];
        $response = new JsonResponse();

        if (!empty($comments)) {
            foreach ($comments as $comment) {
                array_push($commentsParse, [
                    'id' => $comment->getId(),
                    'comment' => $comment->getComment()
                ]);
            }
        }

        return $response->setData(['comments' => $commentsParse]);
    }
}