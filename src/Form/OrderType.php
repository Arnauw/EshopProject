<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'attr' => [
                    'value' => 'Amine',
                ],
            ])
            ->add('lastName', null, [
                'attr' => [
                    'value' => 'Elkhal',
                ],
            ])
            ->add('phoneNumber', null, [
                'attr' => [
                    'value' => '0666666666',
                ],
            ])
            ->add('address', null, [
                'attr' => [
                    'value' => '25 rue des chats',
                ],
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => [
                    'value' => 'Bordeaux',
                ],
            ])
            ->add('isPayingOnDelivery', null, [
                'label' => 'Pay on delivery',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
