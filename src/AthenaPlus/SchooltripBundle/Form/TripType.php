<?php

namespace AthenaPlus\SchooltripBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TripType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('ageGroup')
            ->add('departure')
            ->add('destination')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AthenaPlus\SchooltripBundle\Entity\Trip'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'athenaplus_schooltripbundle_trip';
    }
}
