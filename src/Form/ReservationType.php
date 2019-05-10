<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startBorrowingDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM/dd/yyyy',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Date()
                ],
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'Start Borrowing Date'
            ])
            ->add('endBorrowingDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'MM/dd/yyyy',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Date()
                ],
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'End Borrowing Date'
            ])
            ->add('borrowingQuantity', IntegerType::class, [
                'label' => 'Borrowed Quantity',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Type(['type' => 'integer'])
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Choice([
                        Reservation::FULLY_ACCEPTED,
                        Reservation::PARTIALLY_ACCEPTED,
                        Reservation::IN_PENDING,
                        Reservation::REFUSED
                    ]),
                ],
                'choices' => [
                    Reservation::FULLY_ACCEPTED => Reservation::FULLY_ACCEPTED,
                    Reservation::PARTIALLY_ACCEPTED => Reservation::PARTIALLY_ACCEPTED,
                    Reservation::IN_PENDING => Reservation::IN_PENDING,
                    Reservation::REFUSED => Reservation::REFUSED
                ]
            ])
            ->add('requestedQuantity', IntegerType::class, [
                'label' => 'Requested Quantity',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Type(['type' => 'integer'])
                ],
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'title',
                'constraints' => [
                    new Assert\NotNull()
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'constraints' => [
                    new Assert\NotNull()
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}