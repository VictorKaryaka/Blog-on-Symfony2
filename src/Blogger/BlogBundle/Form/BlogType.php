<?php

namespace Blogger\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManager;

class BlogType extends AbstractType
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
        $authors = [];

        foreach ($this->entityManager->getRepository('BloggerBlogBundle:User')->findAll() as $users) {
            if ($users->getUsername() != $this->username) {
                $authors[$users->getUsername()] = $users->getUsername();
            }
        }

        $builder
            ->add('title')
            ->add('blog')
            ->add('tags')
            ->add('author', ChoiceType::class, [
                'choices' => $authors,
                'multiple' => true,
                'choices_as_values' => true,
                'required' => false,
            ])
            ->add('uploadedFiles', 'file', [
                'required' => false,
                'multiple' => true,
                'label' => 'Select image',
                'data_class' => null,
                'empty_data' => 0
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'blogType';
    }
}