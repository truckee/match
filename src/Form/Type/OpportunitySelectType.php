<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/OpportunitySelectType.php

namespace App\Form\Type;

use App\Entity\Opportunity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpportunitySelectType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $npo = $options['npo'];
        $builder
                ->add('opportunity', EntityType::class,
                        array(
                            'class' => Opportunity::class,
                            'choice_label' => 'oppname',
                            'label' => '',
                            'label_attr' => array(
                                'class' => 'sr-only',
                            ),
                            'query_builder' => function (EntityRepository $er) use ($npo) {
                                return $er->createQueryBuilder('o')
                                        ->where("o.active = '1'")
                                        ->andWhere("o.nonprofit = $npo");
                            },
                            'empty_value' => 'Select opportunity',)
                )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Opportunity::class,
            'npo' => null,
        ]);
    }

}
