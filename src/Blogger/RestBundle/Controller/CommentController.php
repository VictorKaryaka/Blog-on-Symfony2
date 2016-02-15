<?php

namespace Blogger\RestBundle\Controller;

use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Entity\Comment;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Blogger\BlogBundle\Form\CommentType;

class CommentController extends FOSRestController
{
    /**
     * @param Blog $blog
     * @return array
     */
    public function getCommentsAction(Blog $blog)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comments = $entityManager->getRepository('BloggerBlogBundle:Comment')->findBy(['blog' => $blog->getId()]);

        return ['comments' => $comments];
    }

    /**
     * @param Request $request
     * @param Blog $blog
     * @return Comment|\Symfony\Component\Form\FormErrorIterator
     */
    public function postCommentAction(Request $request, Blog $blog)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $username = $this->getUser()->getUsername();
        $user = $entityManager->getRepository('BloggerBlogBundle:User')->findBy(['username' => $username])[0];
        $content = json_decode($request->getContent(), true);
        $content['comment'] = strip_tags($content['comment']);
        $comment = new Comment();
        $comment->setBlog($blog);
        $comment->setUser($this->getUser()->getUsername());
        $comment->setUserId($user);
        $form = $this->createForm(new CommentType(), $comment);
        $form->submit($content);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->getCommentsAction($blog);
        }

        return $form->getErrors();
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return Comment|\Symfony\Component\Form\FormErrorIterator
     */
    public function putCommentAction(Request $request, Comment $comment)
    {
        $content = json_decode($request->getContent(), true);
        $form = $this->createForm(new CommentType(), $comment);
        $form->submit($content);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->getCommentsAction($comment->getBlog());
        }

        return $form->getErrors();
    }

    /**
     * @param Comment $comment
     * @return array
     */
    public function deleteCommentAction(Comment $comment)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment->setComment('This comment is deleted!');
        $entityManager->merge($comment);
        $entityManager->flush();

        return $this->getCommentsAction($comment->getBlog());
    }
}