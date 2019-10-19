<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/OrganizationType.php

namespace App\Form\Type;

use App\Entity\Organization;
use App\Form\Type\FocusesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('orgname')
                ->add('ein')
                ->add('address')
                ->add('city')
                ->add('state')
                ->add('zip')
                ->add('areacode')
                ->add('phone')
                ->add('website')
                ->add('focuses', FocusesType::class)
//            ->add('active')
//            ->add('temp')
//            ->add('addDate')
//            ->add('email')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
        ]);
    }
}
