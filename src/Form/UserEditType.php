<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\StringType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', null,[
                'required'   => false,
            ])
            ->add('lastname', null,[
                'required'   => false,
            ])
            ->add('phoneNumber', null,[
                'required'   => false,
            ])
            ->add('church', null,[
                'required'   => false,
            ])
            ->add('description', CKEditorType::class,array('input_sync' => true, 'required'   => false))
            ->add('showEmail', CheckboxType::class,[
                'required'   => false,
                'attr' => ['class' => 'show']
            ])
            ->add('showPhoneNumber', CheckboxType::class,[
                'required'   => false,
                'attr' => ['class' => 'show']
            ])
            ->add('profilPicture', FileType::class,[
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])

//            ->add('email',EmailType::class,[
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Merci d\'entrer un e-mail',
//                    ]),
//                ],
//                'required' => true,
//                'attr' => ['class' =>'form-control'],
//            ])
            /*->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Editeur' => 'ROLE_EDITOR',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles'
            ])*/
            //->add('password')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
