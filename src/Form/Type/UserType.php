<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/UserType.php

namespace App\Form\Type;

use App\Form\Type\FocusesType;
use App\Form\Type\SkillsType;
use App\Form\Type\OrganizationType;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fname')
                ->add('sname')
                ->add('username')
                ->add('email')
                ->add('password')
//            ->add('roles')
//            ->add('lastLogin')
//            ->add('confirmationToken')
//            ->add('passwordExpiresAt')
//            ->add('enabled')
        ;
        if (true === $options['is_volunteer']) {
            $builder
                    ->add('focuses', FocusesType::class)
                    ->add('skills', SkillsType::class)
            ;
        }
        if (true === $options['is_staff']) {
            $builder
                    ->add('organization', OrganizationType::class)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_volunteer' => false,
            'is_staff' => false,
        ]);
    }

}
