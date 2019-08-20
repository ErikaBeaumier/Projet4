<?php

namespace App\Form;

use App\Entity\Choice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Form\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visit', DateType::class, array(
                'format'=>'dd/MM/yyyy',                
                 'widget' => 'single_text',))
            ->add('halfDay')
            ->add('tickets');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Choice::class,
        ]);
    }
}
