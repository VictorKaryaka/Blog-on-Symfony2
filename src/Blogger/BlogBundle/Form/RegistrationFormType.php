<?php

namespace Blogger\BlogBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use \FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('profilePictureFile', FileType::class, [
            'label' => 'Profile Picture',
            'data_class' => null,
            'required' => false,
            'label_attr'=> ['class' => 'register-label'],
            'attr' => ['class' => 'row'],
        ]);

        $builder->add('captcha', CaptchaType::class , [
            'attr' => ['class' => 'row no-padding'],
            'label_attr'=> ['class' => 'register-label'],
        ]);
    }

    public function getName()
    {
        return 'blogger_blog_registration';
    }
}