<?php

namespace App\Form;

use App\Entity\Visitor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Form\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class VisitorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('birthday', DateType::class, array(
              'widget' => 'single_text','label' => 'Date de naissance'))
            ->add('country', TextType::class, ['label' => 'Pays'])
            ->add('reduc', CheckboxType::class, ['label' => 'Réduction (il sera nécessaire de présenter sa carte d’étudiant, militaire ou équivalent lors de l’entrée pour prouver qu’on bénéficie bien du tarif réduit)','required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Visitor::class,
        ]);
    }
}
