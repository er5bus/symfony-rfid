<?php


namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileType  extends AbstractType
{
    /**
     * {@inheritdoc}
     */
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
                'label' => 'form.email',
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
            ->add('current_password', PasswordType::class, array(
                'label' => 'form.current_password',
                'translation_domain' => 'FOSUserBundle',
                'mapped' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new UserPassword([
                        'message' => 'fos_user.current_password.invalid'
                    ]),
                ),
                'attr' => array(
                    'autocomplete' => 'current-password',
                ),
            ));
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}