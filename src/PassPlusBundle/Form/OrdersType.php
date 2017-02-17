<?php

namespace PassPlusBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PassPlusBundle\Entity\Catalog;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class OrdersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    //Type used by the customer to create an order; only displays catalog entries which are activated
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('catalogs', EntityType::class, array(
            'class'=>'PassPlusBundle:Catalog',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.activated = true');
            },
            'choice_label' => function ($product) {
                return $product->getDescription().' Prix HT : '.$product->getPretaxPrice().'€';
            },
            'label'=>'Produits',
            'expanded'=>true,
            'multiple'=>true,
            'mapped'=>false,
        ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PassPlusBundle\Entity\Orders'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'passplusbundle_orders';
    }


}
