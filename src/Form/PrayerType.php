<?php

namespace App\Form;

use App\Entity\Prayer;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', CKEditorType::class,array('input_sync' => true))
            ->add('security', ChoiceType::class,[
                'choices' => ['Publique' => 0,
                    'Groupe' => 1
                ],
                'required' => false,
                'data' => 0
            ])
            ->add('place', PlaceType::class, [
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prayer::class,
        ]);
    }
}
