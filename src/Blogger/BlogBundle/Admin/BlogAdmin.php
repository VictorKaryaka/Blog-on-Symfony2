<?php

namespace Blogger\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Blogger\BlogBundle\Entity\Blog;
use Doctrine\ORM\EntityManager;

class BlogAdmin extends Admin
{
    private $entityManager;
    protected $baseRoutePattern = 'blogger/blog';

    /**
     * BlogAdmin constructor.
     * @param $entityManager
     */
    public function __construct($code, $class, $baseControllerName, EntityManager $entityManager)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
    }

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id' // field name
    );

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $currentUser = $this->getConfigurationPool()
            ->getContainer()
            ->get('security.token_storage')
            ->getToken()
            ->getUser()
            ->getUsername();

        $authors = $this->entityManager->getRepository('BloggerBlogBundle:User')->getUsers();

        $formMapper->add('title', 'text')
            ->add('author', 'choice', [
                'required' => false,
                'multiple' => true,
                'choices' => $authors,
                'empty_data' => [$currentUser => $currentUser]
            ])
            ->add('blog', 'textarea')
            ->add('tags', 'text')
            ->add('slug', 'text')
            ->add('created', 'datetime')
            ->add('updated', 'datetime');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('title')
            ->add('authors')
            ->add('blog')
            ->add('created')
            ->add('updated')
            ->add('slug')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('author')
            ->add('title')
            ->add('created')
            ->add('updated');
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('authors')
            ->add('title')
            ->add('blog')
            ->add('created')
            ->add('updated');
    }

    /**
     * @param mixed $object
     * @return string
     */
    public function toString($object)
    {
        return $object instanceof Blog ? $object->getBlog() : 'Blog';
    }
}