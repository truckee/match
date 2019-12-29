<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/UserEmailType.php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
                ->add('email', EmailType::class, [
                    'attr' => [
                        'autofocus' => null,
                    ],
                    'label' => 'Email: ',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('submit', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-sm btn-info'
                    ],
                    'label' => 'Submit request'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
        ));
    }

}
