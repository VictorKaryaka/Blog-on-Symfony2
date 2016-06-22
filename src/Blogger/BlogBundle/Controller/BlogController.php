<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\Entity\Blog;
use Blogger\BlogBundle\Entity\Dislikes;
use Blogger\BlogBundle\Entity\Likes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Blogger\BlogBundle\Form\BlogType;
use Blogger\BlogBundle\Form\BlogEditType;
use Symfony\Component\HttpFoundation\Response;

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

        if (!$blog) {
            return $this->redirect($this->generateUrl('BloggerBlogBundle_blog_error', ['error' => 'Blog not found!']));
        }

        $likes = $entityManager
            ->getRepository('BloggerBlogBundle:Likes')->getNumberOfLikes($blog->getId());

        $dislikes = $entityManager
            ->getRepository('BloggerBlogBundle:Dislikes')->getNumberOfDislikes($blog->getId());

        if ($this->getUser()) {
            $form = $this->createForm(
                new BlogEditType($entityManager, $this->getUser()->getUsername()), new Blog())->createView();

            $profilePicture = $this->getUser()->getProfilePicturePath();

            return [
                'blog' => $blog,
                'comments' => $entityManager
                    ->getRepository('BloggerBlogBundle:Comment')->getSortComments($blog->getId()),
                'form' => $form,
                'profilePicture' => $profilePicture,
                'likes' => $likes,
                'dislikes' => $dislikes
            ];
        }

        return [
            'blog' => $blog,
            'comments' => $entityManager
                ->getRepository('BloggerBlogBundle:Comment')->getSortComments($blog->getId()),
            'likes' => $likes,
            'dislikes' => $dislikes
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
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(new BlogType($entityManager, $this->getUser()->getUsername()), new Blog());
        $profilePicture = $this->getUser()->getProfilePicturePath();

        return [
            'form' => $form->createView(),
            'profilePicture' => $profilePicture
        ];
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
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $blog = new Blog();
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(new BlogType($entityManager, $this->getUser()->getUsername()), $blog);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $author = [$this->getUser()->getUsername()];

            if (array_key_exists('author', $request->request->get('blogType'))) {
                $coauthors = $request->request->get('blogType')['author'];

                if ($coauthors) {
                    foreach ($coauthors as $coauthor) {
                        $author[] = $coauthor;
                    }
                }
            }

            $blog->setAuthor($author);
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
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteBlogAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isXmlHttpRequest()) {
                return new Response('', 403);
            } else {
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $blog = $entityManager->find('BloggerBlogBundle:Blog', $id);
        $entityManager->remove($blog);
        $entityManager->flush();

        return $this->redirect($this->generateUrl("BloggerBlogBundle_homepage"));
    }

    /**
     * @Route("/edit/{id}", name="BloggerBlogBundle_blog_edit")
     * @Method("POST")
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateBlogAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isXmlHttpRequest()) {
                return new Response('', 403);
            } else {
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $blog = $entityManager->find('BloggerBlogBundle:Blog', $id);

        if (!empty($request->request->get('blogEditType')['title'])) {
            $blog->setTitle(strip_tags($request->request->get('blogEditType')['title']));
        }

        if (!empty($request->request->get('blogEditType')['blog'])) {
            $blog->setBlog(strip_tags($request->request->get('blogEditType')['blog']));
        }

        if (!empty($request->request->get('blogEditType')['tags'])) {
            $blog->setTags(strip_tags($request->request->get('blogEditType')['tags']));
        }

        if (!empty($request->request->get('blogEditType')['author'])) {
            $authors = $request->request->get('blogEditType')['author'];
            $authors[] = $this->getUser()->getUsername();
            $blog->setAuthor($authors);
        }

        if (!empty($request->files)) {
            $blog->setUploadedFiles($request->files);
        }

        $entityManager->merge($blog);
        $entityManager->flush();
        $images = [];

        foreach ($blog->getImage()->getValues() as $image) {
            $images[] = $image->getName();
        }

        return new JsonResponse([
            'notice' => 'success',
            'images' => $images,
            'title' => $blog->getTitle(),
            'tags' => $blog->getTags(),
            'blog' => $blog->getBlog(),
        ]);
    }

    /**
     * @Route("{id}/setTitleImage/{name}", name="BloggerBlogBundle_blog_setTitleImage")
     * @param Request $request
     * @param $id
     * @param $name
     * @return JsonResponse
     */
    public function setTitleImageAction(Request $request, $id, $name)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isXmlHttpRequest()) {
                return new Response('', 403);
            } else {
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $image = $entityManager->getRepository('BloggerBlogBundle:Image')->getImageByName($id, $name)[0];
        $image->setMain(true);
        $entityManager->merge($image);
        $entityManager->flush();

        return new JsonResponse(['notice' => 'success']);
    }

    /**
     * @Route("{id}/deleteImage/{name}", name="BloggerBlogBundle_blog_deleteImage")
     * @param Request $request
     * @param $name
     * @return JsonResponse
     */
    public function deleteImageAction(Request $request, $name)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->isXmlHttpRequest()) {
                return new Response('', 403);
            } else {
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $image = $entityManager->getRepository('BloggerBlogBundle:Image')->getImageByName(null, $name)[0];
        $entityManager->remove($image);
        $entityManager->flush();

        return new JsonResponse(['notice' => 'success']);
    }

    /**
     * @Route("{id}/get/authors", name="BloggerBlogBundle_blog_getAuthors")
     * @Method("GET")
     * @param $id
     * @return JsonResponse
     */
    public function getAuthorAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $blog = $entityManager->getRepository('BloggerBlogBundle:Blog')->findOneBy(['id' => $id]);

        return new JsonResponse($blog->getAuthor());
    }

    /**
     * @Route("{id}/setLike/like", name="BloggerBlogBundle_blog_setLike")
     * @Method("GET")
     * @param $id
     * @return JsonResponse
     */
    public function setLikeAction($id)
    {
        $user = $this->getUser();

        if (is_null($user)) {
            return new Response('Forbidden', 403);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $blog = $entityManager->getRepository('BloggerBlogBundle:Blog')->findOneById($id);
        $hasLike = $entityManager->getRepository('BloggerBlogBundle:Likes')->hasLike($blog->getId(), $user->getId());
        $hasDislike = $entityManager->getRepository('BloggerBlogBundle:Dislikes')->hasDislike($blog->getId(), $user->getId());

        if (!$hasLike) {
            $likes = new Likes();
            $likes->setBlog($blog);
            $likes->setUserId($user);
            $entityManager->persist($likes);
            $entityManager->flush();
        }

        if ($hasDislike) {
            $entityManager->getRepository('BloggerBlogBundle:Dislikes')->deleteDislike($blog->getId(), $user->getId());
        }

        return new JsonResponse([
            'likes' => $entityManager->getRepository('BloggerBlogBundle:Likes')->getNumberOfLikes($blog->getId()),
            'dislikes' => $entityManager->getRepository('BloggerBlogBundle:Dislikes')->getNumberOfDislikes($blog->getId())
        ]);
    }

    /**
     * @Route("{id}/setDislike/dislike", name="BloggerBlogBundle_blog_setDislike")
     * @Method("GET")
     * @param $id
     * @return JsonResponse
     */
    public function setDislikeAction($id)
    {
        $user = $this->getUser();

        if (is_null($user)) {
            return new Response('Forbidden', 403);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $blog = $entityManager->getRepository('BloggerBlogBundle:Blog')->findOneById($id);
        $hasDislike = $entityManager->getRepository('BloggerBlogBundle:Dislikes')->hasDislike($blog->getId(), $user->getId());
        $hasLike = $entityManager->getRepository('BloggerBlogBundle:Likes')->hasLike($blog->getId(), $user->getId());

        if (!$hasDislike) {
            $likes = new Dislikes();
            $likes->setBlog($blog);
            $likes->setUserId($user);
            $entityManager->persist($likes);
            $entityManager->flush();
        }

        if ($hasLike) {
            $entityManager->getRepository('BloggerBlogBundle:Likes')->deleteLike($blog->getId(), $user->getId());
        }

        return new JsonResponse([
            'likes' => $entityManager->getRepository('BloggerBlogBundle:Likes')->getNumberOfLikes($blog->getId()),
            'dislikes' => $entityManager->getRepository('BloggerBlogBundle:Dislikes')->getNumberOfDislikes($blog->getId())
        ]);
    }
}