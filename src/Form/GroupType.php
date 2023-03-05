<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\GroupCategory;
use App\Entity\Place;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('groupCategories', EntityType::class, [
                'class'=> GroupCategory::class,
                'mapped' => false,
                'multiple' => true
            ])
            ->add('theme', ChoiceType::class, [
                'choices' => Group::THEME
            ])
            ->add('frequence', ChoiceType::class, [
                'choices' => Group::FREQUENCE
            ])
            ->add('location', ChoiceType::class, [
                'choices' => Group::LOCATIONREUNION
            ])
            /*->add('members',EntityType::class, [
                'class' => User::class,
                'multiple' => true
            ])*/
            ->add('description', CKEditorType::class,array('input_sync' => true))
            ->add('image', FileType::class,[
            'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false
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
            'data_class' => Group::class,
        ]);
    }
}
