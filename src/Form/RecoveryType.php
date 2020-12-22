<?php

namespace App\Form;

use App\Entity\UserRecovery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecoveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'datepicker'],
            ])
            ->add('time_from', TimeType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'timepicker'],
            ])
            ->add('time_to', TimeType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'timepicker'],
            ])
            ->add('comment', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserRecovery::class,
        ]);
    }
}
