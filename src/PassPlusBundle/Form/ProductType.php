<?php

namespace PassPlusBundle\Form;

use Doctrine\ORM\EntityRepository;
use PassPlusBundle\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    //Type used by the admin to edit a product line; only displays status entries which are activated
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pretaxPrice')
            ->add('vatRate')
            ->add('state', EntityType::class, array(
                'class'=>'PassPlusBundle:State',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.activated = true');
                },
                'choice_label' => 'name',
                'expanded'=>false,
                'multiple'=>false,
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PassPlusBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'passplusbundle_product';
    }


}
