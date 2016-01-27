<?php

namespace Blogger\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Route\RouteCollection;

class CommentAdmin extends Admin
{
    protected $entityManager;

    public function __construct($code, $class, $baseControllerName, EntityManager $entityManager)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
//        $em = $this->entityManager->getRepository('BloggerBlogBundle:Comment')->createQueryBuilder('comment')
//            ->select('comment')
//            ->from('BloggerBlogBundle:Comment', 'comment')
//            ->where('comment.comment = qweqwe');
//        $em = $this->entityManager->getRepository('BloggerBlogBundle:Comment')->findByBlog(36);

        $formMapper->add('user', 'text')
            ->add('comment', 'textarea')
            ->add('blog', 'entity', [
                'class' => 'Blogger\BlogBundle\Entity\Blog',
                'property' => 'title'
            ])
//            ->add('parentId', 'sonata_type_model', [
//                'label' => 'Comment',
//                'required' => false,
//                'class' => 'Blogger\BlogBundle\Entity\Blog',
//                'property' => 'blog'
//            ])
//            ->add('parentId', 'sonata_type_model', [
//                'required' => false,
//                'query' => $em,
//            ])
            ->add('parentId', 'choice', [
                'required' => false,
                'choices' => [1,2,3,4,5],
            ])
            ->add('created', 'datetime')
            ->add('updated', 'datetime');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('blog', null, ['label' => 'blog ID'])
            ->add('user')
            ->add('comment')
            ->add('parentId')
            ->add('created')
            ->add('updated')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('user')
            ->add('created')
            ->add('updated');
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('user')
            ->add('comment')
            ->add('parentId')
            ->add('created')
            ->add('updated');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('getComments', $this->getRouterIdParameter().'/getComments');
    }
}