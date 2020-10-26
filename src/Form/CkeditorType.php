<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CkeditorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'attr' => ['class' => 'ckeditor'] // on ajoute la classe css
        ]);
    }

    public function getParent() // on utilise l'héritage de formulaire
    {
        return TextareaType::class;
    }
}
