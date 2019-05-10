<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints as Assert;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coverFile', VichImageType::class, [
                'label' => 'Cover of the book',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'image_uri' => true
            ])
            ->add('title', TextType::class, [
                'label' => 'Title of the book',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 200])
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 1000])
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantity',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Type(['type' => 'integer'])
                ],
            ])
            ->add('section', TextType::class, [
                'label' => 'Section',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Length(['max' => 255])
                ],
            ])
            ->add('isbn', TextType::class, [
                'label' => 'International Standard Book Number',
                'constraints' => [
                    new Assert\Isbn()
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'label' => 'Category',
                'constraints' => [
                    new Assert\NotNull()
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
