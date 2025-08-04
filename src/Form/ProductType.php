<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['is_edit-new_form']) {
            $builder
                ->add('name')
                ->add('description')
                ->add('price')
                ->add('subcategory', EntityType::class, [
                    'class' => SubCategory::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                ])
                ->add('image', FileType::class, [
                    'label' => 'Product Image (JPEG or PNG file)',
                    'mapped' => false,
                    'required' => false,
                    'data_class' => null,
                    'constraints' => [
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/jpg',
                                'image/webp',
                                'image/gif',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid JPG/JPEG or PNG image.',
                        ]),
                    ],
                ]);
        }
        if ($options['is_stock_edit_form']) {
            $builder->add('stock', null, [
                'label' => 'Stock Quantity',
                'required' => true,
                'mapped' => false,
                'data' => 0,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'is_edit-new_form' => false,
            'is_stock_edit_form' => false,
        ]);
    }
}
