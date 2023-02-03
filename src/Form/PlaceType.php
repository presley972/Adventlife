<?php

namespace App\Form;

use App\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Adress', HiddenType::class, [
                'attr' => ['autocomplete' => 'street-address', 'class' => 'hiddenField'],
            ])
            ->add('lat', HiddenType::class, [
                'attr' => ['autocomplete' => 'lat', 'class' => 'hiddenField'],
            ])
            ->add('lng', HiddenType::class, [
                'attr' => ['autocomplete' => 'lng', 'class' => 'hiddenField'],
            ])
            ->add('country', HiddenType::class, [
                'attr' => ['autocomplete' => 'country-name', 'class' => 'hiddenField'],
            ])
            ->add('zipCode', HiddenType::class, [
                'attr' => ['autocomplete' => 'postal-code', 'class' => 'hiddenField'],
            ])
            ->add('locality', HiddenType::class, [
                'attr' => ['autocomplete' => 'address-level2', 'class' => 'hiddenField'],
            ])
            ->add('areaRegion', HiddenType::class, [
                'attr' => ['autocomplete' => 'address-level1', 'class' => 'hiddenField'],
            ])
            ->add('street', HiddenType::class, [
                'attr' => ['autocomplete' => 'street-address', 'class' => 'hiddenField'],
            ])
            ->add('streetNumber', HiddenType::class, [
                'attr' => ['autocomplete' => 'address-line2', 'class' => 'hiddenField'],
            ])
            ->add('placeId', HiddenType::class, [
                'attr'=> ['class' => 'hiddenField']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
