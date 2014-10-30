<?php

namespace PetTrafficKing\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PetType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('temperament')
            ->add('breed')
            ->add('color')
            ->add('price')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PetTrafficKing\StoreBundle\Entity\Pet'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pettrafficking_storebundle_pet';
    }
}
