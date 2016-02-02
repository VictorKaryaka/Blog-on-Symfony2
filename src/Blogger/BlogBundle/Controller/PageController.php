<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\Entity\Config;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Blogger\BlogBundle\Entity\Enquiry;
use Blogger\BlogBundle\Form\EnquiryType;

class PageController extends Controller
{
    /**
     * @Route("/", name = "BloggerBlogBundle_homepage")
     * @Method("GET")
     * @Template("BloggerBlogBundle:Page:index.html.twig")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $blogs = $entityManager->getRepository('BloggerBlogBundle:Blog')->getLatestBlogs();
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate($blogs->getQuery(), $request->query->getInt('page', 1),
            $entityManager->find('BloggerBlogBundle:Config', 1)->getBlogsLimit());

        return ['pagination' => $pagination];
    }

    /**
     * @Route("/about", name = "BloggerBlogBundle_about")
     * @Method("GET")
     * @Template("BloggerBlogBundle:Page:about.html.twig")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return [];
    }

    /**
     * @Route("/contact", name = "BloggerBlogBundle_contact")
     * @Method("GET|POST")
     * @Template("BloggerBlogBundle:Page:contact.html.twig")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request)
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact enquiry from symblog')
                    ->setFrom('enquiries@symblog.co.uk')
                    ->setTo($this->container->getParameter('blogger_blog.emails.contact_email'))
                    ->setBody($this->renderView('BloggerBlogBundle:Page:contactEmail.txt.twig', array(
                        'enquiry' => $enquiry)));
                $this->get('mailer')->send($message);

                $this->get('session')->getFlashBag()->add(
                    'blogger-notice',
                    'Ваш запрос успешно отправлен. Спасибо!');

                return $this->redirect($this->generateUrl('BloggerBlogBundle_contact'));
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Template("BloggerBlogBundle:Page:sidebar.html.twig")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sidebarAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tags = $entityManager->getRepository('BloggerBlogBundle:Blog')->getTags();
        $tagWeights = $entityManager->getRepository('BloggerBlogBundle:Blog')->getTagWeights($tags);
        $commentLimit = $this->container->getParameter('blogger_blog.comments.latest_comment_limit');
        $latestComments = $entityManager->getRepository('BloggerBlogBundle:Comment')->getLatestComments($commentLimit);

        return [
            'latestComments' => $latestComments,
            'tags' => $tagWeights
        ];
    }
}