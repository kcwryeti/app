<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'disabled' => $options['is_edit'],
            ])
            ->add('email', EmailType::class, [
                'disabled' => $options['is_edit'],
            ])
            ->add('isActive',CheckboxType::class)
            ->add('plainPassword', PasswordType::class)
            ->add('roles', ChoiceType::class,[
                'empty_data' => true,
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    0 => 'ROLE_ADMIN',
                    1 => 'ROLE_USER',
                    2 => 'ROLE_BLOG_ADMIN',
                ]
            ])->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                if (array_key_exists('roles',$data)) {
                    $form->get('roles')->setData($data['roles']);
                }
            });
        ;

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => User::class,
           'is_edit' => false,
        ]);
    }

}
