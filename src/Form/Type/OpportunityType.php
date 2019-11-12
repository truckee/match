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
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                    'label' => 'Name '
                ])
                ->add('description', TextareaType::class)
                ->add('expiredate', DateType::class, [
                    'label' => 'Expiration ',
                    'widget' => 'single_text',
                    'format'=>'m/d/Y',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                        ]
                )
                ->add('minage', TextType::class, [
                    'label' => 'Minimum age '
                ])
//            ->add('active')
                ->add('groupOk', null, [
                    'label' => 'Group OK?'
                ])
                ->add('background', null, [
                    'label' => 'Background check req\'d?'
                ])
//            ->add('orgid')
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
