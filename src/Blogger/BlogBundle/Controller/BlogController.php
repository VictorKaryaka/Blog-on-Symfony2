<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Blogger\BlogBundle\Form\BlogType;

/**
 * Blog controller.
 */
class BlogController extends Controller
{
    /**
     * @Route("/{id}/{slug}", name = "BloggerBlogBundle_blog_show", requirements={"id": "\d+"})
     * @Template("BloggerBlogBundle:Blog:show.html.twig")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $blog = $entityManager->getRepository('BloggerBlogBundle:Blog')->find($id);

        if (!$blog) {
            return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_error', ['error' => 'Blog not found!']));
        }

        $comments = $entityManager
            ->getRepository('BloggerBlogBundle:Comment')
            ->getCommentsForBlog($blog->getId());

        return [
            'blogs' => $blog,
            'comments' => $comments,
        ];
    }

    /**
     * @Route("/newBlog", name = "BloggerBlogBundle_blog_newBlog")
     * @Template("BloggerBlogBundle:Blog:create.html.twig")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newBlogAction()
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $blog = new Blog();
            $image =new Image();
            $form = $this->createForm(new BlogType($image), $blog);

            return ['form' => $form->createView()];
        } else {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
    }

    /**
     * @Route("/createBlog", name = "BloggerBlogBundle_blog_createBlog")
     * @Template("BloggerBlogBundle:Blog:create.html.twig")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createBlogAction(Request $request)
    {
        $blog = new Blog();
        $form = $this->createForm(new BlogType(), $blog);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $username = $this->getUser()->getUsername();
            $blog->setAuthor($username);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_show', [
                'id' => $blog->getId(),
                'slug' => $blog->getSlug()
            ]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/error/{error}", name = "BloggerBlogBundle_blog_error")
     * @Template("BloggerBlogBundle:Blog:error.html.twig")
     * @param $error
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function errorAction($error)
    {
        return ['error' => $error];
    }
}