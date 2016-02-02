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
     * @Route("/admin/blogslimit")
     * @Method("GET|POST")
     * @Template("BloggerBlogBundle:Admin:blogs_limit.html.twig")
     * @param Request $request
     * @return array
     */
    public function BlogsLimitAction(Request $request)
    {
        $blogsLimit = $request->request->get('blogs_limit');
        $entityManager = $this->getDoctrine()->getManager();
        $configManager = $entityManager->find('BloggerBlogBundle:Config', 1);

        if (!empty($blogsLimit)) {
            if (is_numeric($blogsLimit)) {
                $configManager->setBlogsLimit($blogsLimit);
                $entityManager->persist($configManager);
                $entityManager->flush();

                return ['current_limit' => $blogsLimit];
            } else {
                return false;
            }
        }

        return ['current_limit' => $configManager->getBlogsLimit()];
    }

}