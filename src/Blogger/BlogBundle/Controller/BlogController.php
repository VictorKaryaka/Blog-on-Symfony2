<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Blogger\BlogBundle\Form\BlogType;
use Blogger\BlogBundle\Form\BlogEditType;

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
        $blog = $entityManager->getRepository('BloggerBlogBundle:Blog')->findOneById($id);
        $form = $this->createForm(new BlogEditType(), new Blog());

        if (!$blog) {
            return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_error', ['error' => 'Blog not found!']));
        }

        return [
            'blog' => $blog,
            'comments' => $entityManager
                ->getRepository('BloggerBlogBundle:Comment')->getSortComments($blog->getId()),
            'form' => $form,
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
            $image = new Image();
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

    /**
     * @Route("/delete/{id}", name="BloggerBlogBundle_blog_delete")
     * @Method("GET")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteBlogAction($id)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $entityManager = $this->getDoctrine()->getManager();
            $blog = $entityManager->find('BloggerBlogBundle:Blog', $id);
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl("BloggerBlogBundle_homepage"));
    }

    /**
     * @Route("/edit/{id}", name="BloggerBlogBundle_blog_edit")
     * @Method("POST")
     * @param Request $request
     * @param $id
     */
    public function updateBlogAction(Request $request, $id)
    {
        if (($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $blog = $entityManager->find('BloggerBlogBundle:Blog', $id);
            $titleChange = $request->request->get('blogEditType')['title'];
            $blogChange = $request->request->get('blogEditType')['blog'];
            $tagsChange = $request->request->get('blogEditType')['tags'];
            $image = $request->files;

            if (!empty($titleChange)) {
                $blog->setTitle($titleChange);
            }

            if (!empty($blogChange)) {
                $blog->setBlog($blogChange);
            }

            if (!empty($tagsChange)) {
                $blog->setTags($tagsChange);
            }

            if (!empty($image)) {
                $blog->setUploadedFiles($image);
            }

            $entityManager->merge($blog);
            $entityManager->flush();
            $blogImages = $blog->getImage()->getValues();
            $images = [];

            foreach ($blogImages as $image) {
                $images[] = $image->getName();
            }

            return new JsonResponse([
                'notice' => 'success',
                'images' => $images
            ]);
        }
    }

    /**
     * @Route("{id}/setTitleImage/{name}", name="BloggerBlogBundle_blog_setTitleImage")
     * @param $id
     * @param $name
     * @return JsonResponse
     */
    public function setTitleImageAction($id, $name)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $entityManager->getRepository('BloggerBlogBundle:Image')->getImageByName($id, $name)[0];
            $image->setMain(true);
            $entityManager->merge($image);
            $entityManager->flush();

            return new JsonResponse(['notice' => 'success']);
        }
    }

    /**
     * @Route("{id}/deleteImage/{name}", name="BloggerBlogBundle_blog_deleteImage")
     * @param $id
     * @param $name
     * @return JsonResponse
     */
    public function deleteImage($name)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $entityManager->getRepository('BloggerBlogBundle:Image')->getImageByName(null, $name)[0];
            $entityManager->remove($image);
            $entityManager->flush();

            return new JsonResponse(['notice' => 'success']);
        }
    }
}