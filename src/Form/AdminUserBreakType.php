<?php

namespace App\Form;

use App\Entity\UserBreak;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserBreakType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', ChoiceType::class, [
                'choices' => $options["time_list"]
            ])
            ->add('user_id', ChoiceType::class, [
                'choices'  => $options["user_list"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserBreak::class,
            'time_list' => array('-'),
            'user_list' => array('-'),
        ]);
    }
}
