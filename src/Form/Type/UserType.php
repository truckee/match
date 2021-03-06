<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/UserType.php

namespace App\Form\Type;

use App\Entity\Person;
use App\Form\Type\Field\FocusFieldType;
use App\Form\Type\Field\SkillFieldType;
use App\Validator\Constraints\GloballyUnique;
use App\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('sname', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                        'required' => true,
                    ],
                    'label' => 'Last name: ',
                    'label_attr' => ['class' => 'mr-2'],
                    'constraints' => [new NotBlank(['message' => "Last name is required"])],
                ])
                ->add('fname', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                        'required' => true,
                        'autofocus' => null,
                    ],
                    'label' => 'First name: ',
                    'label_attr' => ['class' => 'mr-2'],
                    'constraints' => [new NotBlank(['message' => "First name is required"])],
                ])
        ;

        /*
         * New Volunteer: Person->hasRole('ROLE_VOLUNTEER');  $data instanceof Person::class; id === null
         * New Rep: $user === null
         * Volunteer profile: Person->hasRole('ROLE_VOLUNTEER');  $data instanceof Person::class; id not null
         * Rep profile: Person->hasRole('ROLE_REP');  $data instanceof Person::class; id not null
         */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
            if (null !== $user && $user->hasRole('ROLE_VOLUNTEER')) {
                $form
                        ->add('focuses', FocusFieldType::class)
                        ->add('skills', SkillFieldType::class)
                ;
                if (false === $form->getConfig()->getOption('register')) {
                    $form->add('receiveEmail');
                }
            }

            // new users
            if ((null !== $user && null === $user->getId()) || null === $user) {
                $form->add('email', null, [
                    'attr' => [
                        'class' => 'mb-2',
                        'size' => '15',
                        'required' => true,
                    ],
                    'label' => 'Email: ',
                    'label_attr' => ['class' => 'mr-2'],
                    'constraints' => [
                        new NotBlank(['message' => "Email is required"]),
                        new GloballyUnique(),
                    ]
                ])
                ;
            }

            if ($form->getConfig()->getOption('password')) {
                $form->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'help' => '(At least 6 characters, incl. upper & lower case letters and at least one number)',
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
                ));
            }

            if (null === $user && null !== $form->getConfig()->getOption('npo_id')) {
                $form->add('npoid', HiddenType::class, [
                    'mapped' => false,
                    'data' => $form->getConfig()->getOption('npo_id')
                ]);
            }
        }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'required' => false,
            'npo_id' => null,
            'register' => false,
            'password' => true,
        ]);
    }

}
