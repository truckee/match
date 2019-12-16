<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/OpportunityType.php

namespace App\Form\Type;

use App\Entity\Opportunity;
use App\Form\Type\Field\SkillFieldType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpportunityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('oppname', null, [
                    'label' => 'Name ',
                    'attr' => [
                        'class' => 'mb-2',
                        'required' => true,
                    ],
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('description', TextareaType::class, [
                    'attr' => [
                        'class' => 'mb-2',
                        'required' => true,
                    ],
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add(
                    'expiredate',
                    DateType::class,
                    [
                    'label' => 'Expiration ',
                    'widget' => 'single_text',
                    'format' => 'M/d/y',
                    'html5' => false,
                    'attr' => [
                        'class' => 'js-datepicker mb-2',
                        'required' => true,
                    ],
                    'label_attr' => ['class' => 'mr-2'],
                        ]
                )
                ->add('minage', ChoiceType::class, [
                    'choices' => [
                        '' => '',
                        '5' => '5',
                        '12' => '12',
                        '18' => '18',
                        '21' => '21',
                        '55' => '55',
                    ],
                    'label' => 'Minimum age ',
                ])
                ->add('active', CheckboxType::class, [
                    'label' => 'Active?'
                ])
                ->add('groupOk', null, [
                    'label' => 'Group OK?'
                ])
                ->add('background', null, [
                    'label' => 'Background check req\'d?'
                ])
                ->add('skills', SkillFieldType::class, [
                    'label' => false,
                ])
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
