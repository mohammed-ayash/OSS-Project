<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class ProductType extends AbstractType
{
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
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('picture',FileType::class,[
                'label'=> 'Image',
                'required'=> false,
                'mapped'=> false,
                'constraints' => $imageConstraints
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
