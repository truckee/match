<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/UserType.php

namespace App\Form\Type;

use App\Entity\Volunteer;
use App\Form\Type\Field\FocusFieldType;
use App\Form\Type\Field\SkillFieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * 
 */
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
                    ],
                    'label' => 'First name: ',
                    'label_attr' => ['class' => 'mr-2'],
                    'constraints' => [new NotBlank(['message' => "First name is required"])],
                ])
        ;
        if (Volunteer::class === $options['data_class']) {
            $builder->add('receiveEmail')
                    ->add('focuses', FocusFieldType::class)
                    ->add('skills', SkillFieldType::class)
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'required' => false,
            'register' => false,
        ]);
    }

}