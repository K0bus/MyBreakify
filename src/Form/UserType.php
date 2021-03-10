<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            /*->add('password', PasswordType::class, [
                "required" => false,
                "empty_data" => "",
                "help" => "Si aucun changement, laisser ce champs vide."
            ])*/
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add(
                'roles', 'choice', [
                    'choices' => ['Utilisateur' => 'ROLE_USER', 'Responsable' => 'ROLE_N1', 'Administrateur' => 'ROLE_ADMIN'],
                    'expanded' => true,
                    'multiple' => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
