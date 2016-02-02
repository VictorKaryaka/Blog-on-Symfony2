<?php

namespace Blogger\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Blogger\BlogBundle\Entity\Blog;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class BlogAdmin extends Admin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', 'text')
            ->add('author', HiddenType::class, [
                'data' => $this->getConfigurationPool()
                    ->getContainer()
                    ->get('security.token_storage')
                    ->getToken()
                    ->getUser()
                    ->getUsername()
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
            ->add('author')
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
        $showMapper->add('author')
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