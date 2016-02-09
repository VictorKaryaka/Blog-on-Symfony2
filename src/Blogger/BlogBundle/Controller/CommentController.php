<?php

namespace Blogger\BlogBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
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
        $comment = new Comment();
        $comment->setBlog($this->getBlog($blog_id));
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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction(Request $request, $blog_id)
    {
        $response = new JsonResponse();
        $commentMessage = $request->request->get('commentType');
        $commentMessage['comment'] = strip_tags($commentMessage['comment']);
        $request->request->set('commentType', $commentMessage);
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

            return $response->setData([
                'id' => $comment->getId(),
                'user' => $comment->getUser(),
                'comment' => $comment->getComment(),
                'created' => $comment->getCreated(),
                'parentId' => $comment->getParentId()
            ]);
        }

        return [
            'comment' => $comment,
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("{id}/comment/edit/{blog_id}", name = "BloggerBlogBundle_comment_edit", requirements={"blog_id": "\d+"})
     * @Method("POST")
     * @param Request $request
     * @param $blog_id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editCommentAction(Request $request, $blog_id)
    {
        if (($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $editComment = strip_tags($request->request->get('comment'));
            $comment = $entityManager->find('BloggerBlogBundle:Comment', $blog_id);

            return $this->updateComment($entityManager, $comment, $editComment);
        }
    }

    /**
     * @Route("{id}/comment/delete/{comment_id}", name = "BloggerBlogBundle_comment_delete", requirements={"comment_id": "\d+"})
     * @param $comment_id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteCommentAction($comment_id)
    {
        if (($this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment = $entityManager->find('BloggerBlogBundle:Comment', $comment_id);

            return $this->updateComment($entityManager, $comment, 'This comment is deleted!');
        }
    }

    /**
     * @param ObjectManager $entityManager
     * @param Comment $comment
     * @param $message
     * @return JsonResponse
     */
    private function updateComment(ObjectManager $entityManager, Comment $comment, $message)
    {
        $comment->setComment($message);
        $entityManager->merge($comment);
        $entityManager->flush();

        return new JsonResponse([
            'notice' => 'success',
            'comment' => $comment->getComment(),
        ]);
    }

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