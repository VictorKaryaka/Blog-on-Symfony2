<?php

namespace Blogger\BlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Comment;
use Blogger\BlogBundle\Form\CommentType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 * @package Blogger\BlogBundle\Controller
 */
class CommentController extends Controller
{
    /**
     * @Route("/blog_id/{blog_id}", name="BloggerBlogBundle:Comment:newFormComment")
     * @Method("GET")
     * @Template("BloggerBlogBundle:Comment:form.html.twig")
     * @param $blog_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newFormCommentAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);
        $comment = new Comment();
        $comment->setBlog($blog);
        $form = $this->createForm(new CommentType(), $comment);

        return [
            'comment' => $comment,
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/comment/{blog_id}", name = "BloggerBlogBundle_comment_create", requirements={"blog_id": "\d+"})
     * @Method("GET|POST")
     * @Template("BloggerBlogBundle:Comment:create.html.twig")
     * @param Request $request
     * @param $blog_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $blog_id)
    {
        $blog = $this->getBlog($blog_id);
        $comment = new Comment();
        $username = $this->getUser()->getUsername();
        $comment->setBlog($blog);
        $comment->setUser($username);
        $form = $this->createForm(new CommentType(), $comment);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $commentArray = [
                'id' => $comment->getId(),
                'user' => $comment->getUser(),
                'comment' => $comment->getComment(),
                'created' => $comment->getCreated(),
                'parentId' => $comment->getParentId()
            ];

            return new JsonResponse($commentArray);
        }

        return [
            'comment' => $comment,
            'form' => $form->createView()
        ];
    }

    public function addCommentAction(){}

    /**
     * @param $blog_id
     * @return \Blogger\BlogBundle\Entity\Blog|object
     */
    protected function getBlog($blog_id)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($blog_id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $blog;
    }
}