<?php

namespace Blogger\RestBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Form\BlogType;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends FOSRestController
{
    /**
     * @param Blog $blog
     * @return array
     */
    public function getBlogAction(Blog $blog)
    {
        if ($this->getUser()) {
            return ['blog' => $blog];
        }
    }

    public function getBlogsAction()
    {
        if ($this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();
            $blogs = $entityManager->getRepository('BloggerBlogBundle:Blog')->findAll();

            return ['blogs' => $blogs];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function postBlogAction(Request $request)
    {
        if ($this->getUser()) {
            $content = json_decode($request->getContent(), true);
            $blog = new Blog();
            $blog->setAuthor($this->getUser()->getUsername());
            $form = $this->createForm(new BlogType(), $blog);
            $form->submit($content);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($blog);
                $entityManager->flush();

                return $this->getBlogAction($blog);
            }

            return $form->getErrors();
        }
    }

    /**
     * @param Request $request
     * @param Blog $blog
     * @return array
     */
    public function putBlogAction(Request $request, Blog $blog)
    {
        if ($this->getUser()) {
            $content = json_decode($request->getContent(), true);
            $entityManager = $this->getDoctrine()->getManager();
            $blog->setAuthor($this->getUser()->getUsername());
            $form = $this->createForm(new BlogType(), $blog);
            $form->submit($content);

            if ($form->isValid()) {
                $entityManager->persist($blog);
                $entityManager->flush();

                return $this->getBlogAction($blog);
            }

            return $form->getErrors();
        }
    }

    /**
     * @param Blog $blog
     * @return array
     */
    public function deleteBlogAction(Blog $blog)
    {
        if ($this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($blog);
            $entityManager->flush();

            return $this->getBlogsAction();
        }
    }
}
