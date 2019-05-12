<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Entity\User;
use App\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MakeReservationType extends AbstractType
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
            ->add('status', HiddenType::class, [
                'data' => Reservation::IN_PENDING,
                'constraints' => [
                    new Assert\EqualTo(['value' => Reservation::IN_PENDING])
                ],
            ])
            ->add('requestedQuantity', IntegerType::class, [
                'label' => 'Requested Quantity',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Type(['type' => 'integer']),
                    new Assert\GreaterThanOrEqual(['value' => 1])
                ],
            ])
            ->add('book', HiddenEntityType::class, [
                'class' => Book::class
            ])
            ->add('user', HiddenEntityType::class, [
                'class' => User::class
            ]);
    }

    /**
     * @param Reservation $value
     * @param ExecutionContextInterface $context
     * @throws \Exception
     */
    public function validateInterval($value, ExecutionContextInterface $context)
    {
        if (!is_null($value->getStartBorrowingDate()) && !is_null($value->getEndBorrowingDate())) {

            if ($value->getStartBorrowingDate()->diff(new \DateTime('now'))->format('%a') > 0) {
                $context
                    ->buildViolation('This value is not a valid date.')
                    ->atPath('startBorrowingDate')
                    ->addViolation();
            }

            if ($value->getStartBorrowingDate() > $value->getEndBorrowingDate()) {
                $context
                    ->buildViolation('This value should be {{ limit }} or less.')
                    ->setParameter('{{ limit }}', $value->getStartBorrowingDate()->format('m/d/Y'))
                    ->atPath('startBorrowingDate')
                    ->addViolation();
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'constraints' => [
                new Assert\Callback(array($this, 'validateInterval'))
            ]
        ]);
    }
}