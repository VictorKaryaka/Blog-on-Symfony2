<?php

namespace Blogger\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Blogger\BlogBundle\Entity\User;

class UserAdmin extends Admin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('username', 'text')
            ->add('email', 'text')
            ->add('enabled', 'checkbox', ['required' => false])
            ->add('lastLogin', 'datetime')
            ->add('role', 'choice', [
                'multiple' => true,
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_API' => 'ROLE_API',
                ],
            ])
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'The password fields must match!',
                'first_options' => ['label' => 'password'],
                'second_options' => ['label' => 'Repeat Password'],
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('username')
            ->add('enabled');
    }

    /**
     * @param ListMapper $listMapper \
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('username')
            ->add('email')
            ->add('enabled')
            ->add('lastLogin')
            ->add('userRoles')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('lastLogin');
    }

    /**
     * @param mixed $object
     * @return string
     */
    public function toString($object)
    {
        return $object instanceof User ? $object->getUsername() : 'User';
    }
}