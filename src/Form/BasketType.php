<?php

namespace App\Form;

use App\Entity\ProductPurchase;
use App\Entity\Basket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opts = array('error_bubbling' => true);

        $builder
            ->add('createdAt', DateTimeType::class, $opts)
            // ->add('productPurchase', EntityType::class, array(
            //     'class' => ProductPurchase::class,
            //     'choice_label'  => 'id',
            //     'error_bubbling' => true,
            //     'multiple' => true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Basket::class
        ]);
    }
}
