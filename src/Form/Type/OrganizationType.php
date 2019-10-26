<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/OrganizationType.php

namespace App\Form\Type;

use App\Form\Type\Field\FocusFieldType;
use App\Entity\Organization;
use App\Entity\Staff;
use App\Form\Type\NewUserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('orgname', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'Name: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('ein', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'EIN: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('address', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'Address: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('city', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'City: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('state', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'State: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('zip', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'Zip: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('area_code', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'Area code: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('phone', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'Phone: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('website', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'Web site: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('focuses', FocusFieldType::class)
                ->add('staff', NewUserType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
            'required' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'org';
    }
}
