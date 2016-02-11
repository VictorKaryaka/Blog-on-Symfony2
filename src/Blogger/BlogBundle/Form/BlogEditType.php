<?php

namespace Blogger\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManager;

class BlogEditType extends AbstractType
{
    private $entityManager;
    private $username;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $username)
    {
        $this->entityManager = $entityManager;
        $this->username = $username;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $authors = $this->entityManager->getRepository('BloggerBlogBundle:User')
            ->getUsersWithoutCurrent($this->username);

        $builder
            ->add('title', 'text', ['required' => false])
            ->add('blog', 'textarea', ['required' => false])
            ->add('tags', 'text', ['required' => false])
            ->add('uploadedFiles', 'file', [
                'required' => false,
                'multiple' => true,
                'label' => 'Add image',
                'data_class' => null,
                'empty_data' => 0
            ])
            ->add('author', ChoiceType::class, [
                'required' => false,
                'choices' => $authors,
                'multiple' => true,
                'choices_as_values' => true,
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blogEditType';
    }
}