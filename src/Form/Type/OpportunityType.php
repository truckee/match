<?php

namespace App\Form\Type;

use App\Entity\Opportunity;
use App\Form\Type\Field\SkillFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpportunityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oppname')
            ->add('description')
            ->add('expiredate')
//            ->add('addDate')
//            ->add('lastupdate')
            ->add('minage')
//            ->add('active')
            ->add('groupOk')
            ->add('background')
//            ->add('orgid')
            ->add('skills', SkillFieldType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Opportunity::class,
            'required' => false,
        ]);
    }
}
