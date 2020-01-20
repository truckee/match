<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/NewPasswordType.php

namespace App\Form\Type;

use App\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of NewPasswordType
 *
 * @author George Brooks <truckeesolutions@gmail.com>
 */
class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(['message' => "Password may not empty"]),
                        new PasswordRequirements()
                        ],
                    'invalid_message' => 'Passwords do not match',
                    'first_options' => [
                        'attr' => [
                            'class' => 'mb-2',
                            'size' => '15',
                            'required' => true,
                            'autofocus' => null,
                        ],
                        'label' => 'Password:',
                        'label_attr' => ['class' => 'mr-2'],
                        'required' => true,
                    ],
                    'second_options' => [
                        'attr' => [
                            'class' => 'mb-2',
                            'size' => '15',
                            'required' => true,
                        ],
                        'label' => 'Confirm:',
                        'label_attr' => ['class' => 'mr-2'],
                        'required' => true,
                    ],
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
        ));
    }
}
