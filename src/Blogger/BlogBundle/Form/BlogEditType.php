<?php

namespace Blogger\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BlogEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', ['required' => false])
            ->add('blog', 'textarea', ['required' => false])
            ->add('tags', 'text', ['required' => false]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blogEditType';
    }
}