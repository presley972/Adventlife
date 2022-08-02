<?php

namespace App\Form;

use App\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Adress', TextType::class, [
                'attr' => ['autocomplete' => 'address-line1'],
            ])
            ->add('lat')
            ->add('lng')
            ->add('country')
            ->add('zipCode')
            ->add('locality')
            ->add('areaRegion')
            ->add('areaDepartement')
            ->add('street')
            ->add('streetNumber')
            ->add('placeId')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
