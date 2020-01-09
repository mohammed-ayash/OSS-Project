<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class UserType extends AbstractType
{
    //for build new form
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $imageConstraints = [
            new Assert\File([
                'mimeTypes' => [
                    "image/jpg",
                    "image/jpeg"
                ],
                'mimeTypesMessage' => 'Please select jpg image'
            ])
        ];

        $builder
            ->add('username')
            ->add('email')
            ->add('password', PasswordType::class)
            ->add('picture',FileType::class,[
                'label'=> 'Image',
                'required'=> false,
                'mapped'=> false,
                'constraints' => $imageConstraints
            ])
            ->add('birthday')
            ->add('type', ChoiceType::class, [
                'choices' => ['Owner Shop' => true, 'Customer' => false],
            ])
            ->add('country')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'picture' => null,
        ]);
    }
}
