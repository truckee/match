<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/NewUserType.php

namespace App\Form\Type;

use App\Form\Type\Field\FocusFieldType;
use App\Form\Type\Field\SkillFieldType;
use App\Entity\Staff;
use App\Entity\Volunteer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('sname', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'Last name: ',
                    'label_attr' => ['class' => 'mr-2'],
                    'constraints' => [new NotBlank(['message' => "Last name is required"])],
                ])
                ->add('fname', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'First name: ',
                    'label_attr' => ['class' => 'mr-2'],
                    'constraints' => [new NotBlank(['message' => "First name is required"])],
                ])
                ->add('email', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                    ],
                    'label' => 'Email: ',
                    'label_attr' => ['class' => 'mr-2'],
                    'constraints' => [new NotBlank(['message' => "Email is required"])],
                ])
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'constraints' => [new NotBlank(['message' => "Password may not empty"])],
                    'invalid_message' => 'Passwords do not match',
                    'first_options' => [
                        'attr' => [
                            'class' => 'mb-2',
                            'size' => '15',
                        ],
                        'label' => 'Password:',
                        'label_attr' => ['class' => 'mr-2'],
                        'required' => true,
                    ],
                    'second_options' => [
                        'attr' => [
                            'class' => 'mb-2',
                            'size' => '15',
                        ],
                        'label' => 'Confirm:',
                        'label_attr' => ['class' => 'mr-2'],
                        'required' => true,
                    ],
                ))
                ->add('confirmationToken', HiddenType::class, [
                    'data' => md5(uniqid(rand(), true)),
                ])
        ;
        if (Volunteer::class === $options['data_class']) {
            $builder
                    ->add('focuses', FocusFieldType::class)
                    ->add('skills', SkillFieldType::class)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Staff::class,
            'required' => false,
            'error_bubbling' => true,
        ]);
    }

}
