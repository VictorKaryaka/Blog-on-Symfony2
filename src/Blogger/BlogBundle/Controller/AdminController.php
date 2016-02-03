<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\Entity\Config;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Controller\CoreController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends CoreController
{
    /**
     * @Route("/admin/settings")
     * @Method("GET|POST")
     * @Template("BloggerBlogBundle:Admin:settings.html.twig")
     * @param Request $request
     * @return array
     */
    public function settingsAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $config = $entityManager->find('BloggerBlogBundle:Config', 1);

        if ($request->request->count() > 0) {
            switch (key($request->request->all())) {
                case 'blogs_limit': {
                    return $this->setBlogLimit($request, $entityManager, $config);
                }
                case 'comments_limit': {
                    return $this->setCommentLimit($request, $entityManager, $config);
                }
                case 'contact_email': {
                    return $this->setContactEmail($request, $entityManager, $config);
                }
            }
        } else {
            return [
                'current_blog_limit' => $config->getBlogsLimit(),
                'current_comment_limit' => $config->getCommentsLimit(),
                'contact_email' => $config->getContactEmail()
            ];
        }
    }

    /**
     * @param Request $request
     * @param $entityManager
     * @param $config
     * @return array|bool
     */
    private function setBlogLimit(Request $request, $entityManager, $config)
    {
        $blogsLimit = $request->request->get('blogs_limit');

        if (!empty($blogsLimit)) {
            if (is_numeric($blogsLimit)) {
                $config->setBlogsLimit($blogsLimit);
                $entityManager->persist($config);
                $entityManager->flush();

                return [
                    'current_blog_limit' => $blogsLimit,
                    'current_comment_limit' => $config->getCommentsLimit(),
                    'contact_email' => $config->getContactEmail()
                ];
            } else {
                return false;
            }
        }
    }

    /**
     * @param Request $request
     * @param $entityManager
     * @param $config
     * @return array|bool
     */
    private function setCommentLimit(Request $request, $entityManager, $config)
    {
        $commentsLimit = $request->request->get('comments_limit');

        if (!empty($commentsLimit)) {
            if (is_numeric($commentsLimit)) {
                $config->setCommentsLimit($commentsLimit);
                $entityManager->persist($config);
                $entityManager->flush();

                return [
                    'current_blog_limit' => $config->getBlogsLimit(),
                    'current_comment_limit' => $commentsLimit,
                    'contact_email' => $config->getContactEmail()
                ];
            } else {
                return false;
            }
        }
    }

    /**
     * @param Request $request
     * @param $entityManager
     * @param $config
     * @return array|bool
     */
    private function setContactEmail(Request $request, $entityManager, $config)
    {
        $contactEmail = $request->request->get('contact_email');

        if (!empty($contactEmail)) {
            if (is_string($contactEmail)) {
                $config->setContactEmail($contactEmail);
                $entityManager->persist($config);
                $entityManager->flush();

                return [
                    'current_blog_limit' => $config->getBlogsLimit(),
                    'current_comment_limit' => $config->getCommentsLimit(),
                    'contact_email' => $contactEmail
                ];
            } else {
                return false;
            }
        }
    }
}