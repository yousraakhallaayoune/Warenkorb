<?php

namespace App\Form;

use App\Entity\ProductPurchase;
use App\Entity\Product;
use App\Entity\Basket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $opts = array('error_bubbling' => false);

        $builder
            ->add('quantity', NumberType::class, $opts)
            ->add('product', EntityType::class, array(
                'class' => Product::class,
                'error_bubbling' => true))
            ->add('basket', EntityType::class, array(
                'class' => Basket::class,
                'error_bubbling' => true))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPurchase::class
        ]);
    }
}
