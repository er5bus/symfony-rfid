<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 100])
                ],
            ])
            ->add('code', TextType::class, [
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 255])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'constraints' => [
                new UniqueEntity([
                    'fields' => 'code'
                ]),
                new UniqueEntity([
                    'fields' => 'title'
                ])
            ]
        ]);
    }
}
