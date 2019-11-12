<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/FocusesType.php

namespace App\Form\Type;

use App\Entity\Focus;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Description of FocusType.
 *
 */
class FocusesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('focuses', EntityType::class, [
                    'class' => Focus::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                                ->orderBy('f.focus', 'ASC')
                                ->where("f.focus <> 'All'")
                                ->andWhere("f.enabled = true");
                    },
                    'label' => false,
                    'choice_label' => 'focus',
//                    'choice_attr' => function($choice, $key, $value) {
//                        // adds a class like attending_yes, attending_no, etc
//                        return ['class' => 'form-check-inline'];
//                    },
                    'multiple' => true,
                    'expanded' => true,
                ])
        ;
    }

//    public function getName()
//    {
//        return 'focuses';
//    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'required' => false,
        ));
    }

}
