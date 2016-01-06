<?php

namespace Blogger\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BlogType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('blog', 'textarea')
            ->add('tags')
            ->add('uploadedFiles', 'file', [
                'required' => false,
                'multiple' => true,
                'label' => 'Select image',
                'data_class' => null,
                'empty_data' => null
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blogType';
    }
}