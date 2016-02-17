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
        return ['blog' => $blog];
    }

    public function getBlogsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $blogs = $entityManager->getRepository('BloggerBlogBundle:Blog')->findAll();

        return ['blogs' => $blogs];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function postBlogAction(Request $request)
    {
        return $this->updateBlog($request, null, true);
    }

    /**
     * @param Request $request
     * @param Blog $blog
     * @return array
     */
    public function putBlogAction(Request $request, Blog $blog)
    {
        return $this->updateBlog($request, $blog);
    }

    /**
     * @param Blog $blog
     * @return array
     */
    public function deleteBlogAction(Blog $blog)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($blog);
        $entityManager->flush();

        return $this->getBlogsAction();
    }

    /**
     * @param Request $request
     * @param Blog|null $blog
     * @param bool|false $post
     * @return array|\Symfony\Component\Form\FormErrorIterator
     */
    private function updateBlog(Request $request, Blog $blog = null, $post = false)
    {
        $content = json_decode($request->getContent(), true);
        $content['title'] = strip_tags($content['title']);
        $content['blog'] = strip_tags($content['blog']);
        $content['tags'] = strip_tags($content['tags']);

        if (!empty($content['author'])) {
            $coauthors = $content['author'];
            unset($content['author']);
        }

        $username = $this->getUser()->getUsername();
        $entityManager = $this->getDoctrine()->getManager();

        if (empty($blog)) {
            $blog = new Blog();
        }

        $form = $this->createForm(new BlogType($entityManager, $username), $blog);
        $form->submit($content);

        if ($form->isValid()) {
            $authors = [$username];

            if (!empty($coauthors)) {
                foreach ($coauthors as $author) {
                    $authors[] = $author;
                }
            }

            $blog->setAuthor($authors);

            if ($post) {
                $entityManager->persist($blog);
            } else {
                $entityManager->merge($blog);
            }

            $entityManager->flush();

            return $this->getBlogAction($blog);
        }

        return $form->getErrors();
    }
}
