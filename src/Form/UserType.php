<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First name',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 100])
                ],
                'attr' => ['placeholder' => 'First name']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last name',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 100])
                ],
                'attr' => ['placeholder' => 'Last name']
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Birthday',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Date()
                ],
                'attr' => ['placeholder' => 'Birthday', 'class' => 'datepicker'],
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM/dd/yyyy'
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Address',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 255])
                ],
                'attr' => ['placeholder' => 'Address']
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Phone Number',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 30])
                ],
                'attr' => ['placeholder' => 'Phone Number']
            ])
            ->add('email', EmailType::class, array(
                'label' =>  'form.email',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Email(),
                    new Assert\Length(['max' => 180])
                ],
                'attr' => ['placeholder' => 'form.email'],
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('username', null, array(
                'label' => 'form.username',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 180])
                ],
                'attr' => ['placeholder' => 'form.username'],
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'constraints' => [
                    new Assert\Length(['max' => 200])
                ],
                'options' => array(
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_options' => array('label' => 'form.password', 'attr' => ['placeholder' => 'form.password']),
                'second_options' => array('label' => 'form.password_confirmation', 'attr' => ['placeholder' => 'form.password_confirmation']),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('enabled', CheckboxType::class, [
                'label' => 'Enabled'
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'User Roles',
                'multiple' => true,
                'choices' => [
                    'Simple User' => 'ROLE_USER',
                    'Librarian' => 'ROLE_LIBRARIAN'
                ],
                'constraints' => [
                    new Assert\NotNull()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity([
                    'fields' => 'username'
                ]),
                new UniqueEntity([
                    'fields' => 'email'
                ])
            ]
        ]);
    }
}
