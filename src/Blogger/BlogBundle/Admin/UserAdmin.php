<?php

namespace Blogger\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

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
//            ->add('password', 'repeated', [
//                'type' => 'password',
//                'invalid_message' => 'The password fields must match!',
//                'first_options' => ['label' => 'password'],
//                'second_options' => ['label' => 'Repeat Password']
//            ])
           ;
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
     * @param ListMapper $listMapper\
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('username')
            ->add('email')
            ->add('enabled')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

//    /**
//     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
//     * @return void
//     */
//    protected function configureShowField(ShowMapper $showMapper)
//    {
//        $showMapper
//            ->add('id', null, array('label' => 'Идентификатор'))
//            ->add('username', null, array('label' => 'Пользователь'))
//            ->add('email', null, array('label' => 'E-mail'))
//            ->add('enabled', null, array('label' => 'Активность'));
//    }
}