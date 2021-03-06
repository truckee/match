<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/UserEmailType.php

namespace App\Form\Type\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of EmailType
 *
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class UserEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email', null, [
                            'attr' => [
                                'class' => 'mb-2',
                                'size' => '15',
                                'required' => true,
                            ],
                            'label' => 'Email: ',
                            'label_attr' => ['class' => 'mr-2'],
                            'constraints' => [
                                new NotBlank(['message' => "Email is required"])
                                ]
                        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'validation_groups'
        ));
    }
}
