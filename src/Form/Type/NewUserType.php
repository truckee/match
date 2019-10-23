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
//use App\Form\Type\OrganizationType;
use App\Entity\Staff;
use App\Entity\Volunteer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewUserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fname', null, [
                    'attr' => ['class' => 'mb-2'],
                    'label' => 'First name ',
                    'label_attr' => ['class' => 'mr-2']
                ])
                ->add('sname', null, [
                    'attr' => ['class' => 'mb-2'],
                    'label' => 'Last name ',
                    'label_attr' => ['class' => 'mr-2']
                ])
                ->add('email', null, [
                    'attr' => ['class' => 'mb-2'],
                    'label' => 'Email ',
                    'label_attr' => ['class' => 'mr-2']
                ])
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'first_options' => [
                        'attr' => ['class' => 'mb-2'],
                        'label' => 'Password',
                        'label_attr' => ['class' => 'mr-2']
                    ],
                    'second_options' => [
                        'attr' => ['class' => 'mb-2'],
                        'label' => 'Password',
                        'label_attr' => ['class' => 'mr-2']
                    ],
                ))
        ;
        if (Volunteer::class === $options['data_class']) {
            $builder
                    ->add('focuses', FocusFieldType::class)
                    ->add('skills', SkillFieldType::class)
            ;
    }
        if (Staff::class === $options['data_class']) {
            $builder
//                    ->add('organization', OrganizationType::class)
                    ->add('focuses', FocusFieldType::class)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => null,
            'is_volunteer' => false,
            'is_staff' => false,
        ]);
    }

}
