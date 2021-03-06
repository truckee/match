<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/NonprofitType.php

namespace App\Form\Type;

use App\Form\Type\Field\FocusFieldType;
use App\Entity\Nonprofit;
use App\Entity\Person;
use App\Form\Type\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NonprofitType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('orgname', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                        'required' => true,
                        'autofocus' => null,
                    ],
                    'label' => 'Name: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('ein', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                        'required' => true,
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
                ->add('phone', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                        'placeholder' => '(___) ___-____',
                    ],
                    'label' => 'Phone: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('website', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                        'placeholder' => 'http://'
                    ],
                    'label' => 'Web site: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('focuses', FocusFieldType::class)
        ;

        if (true === $options['register']) {
            $builder->add('rep', UserType::class, [
                'label' => false,
                'mapped' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Nonprofit::class,
            'required' => false,
            'register' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'org';
    }

}
