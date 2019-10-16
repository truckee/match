<?php
/*
 * This file is part of the Truckee\Match package.
 * 
 * (c) George W. Brooks
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Truckee\MatchBundle\Form\FocusesType.php

namespace App\Form\Type;

use App\Form\Type\FocusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Description of FocusType.
 *
 */
class FocusesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('focuses', CollectionType::class, ['entry_type' => FocusType::class,
            ])
//            ->add('save', SubmitType::class,
//                array(
//                'label' => 'Save',
//                'attr' => array(
//                    'class' => 'btn-xs',
//                ),
//            ))
        ;
    }

//    public function getName()
//    {
//        return 'focuses';
//    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
        ));
    }
}
