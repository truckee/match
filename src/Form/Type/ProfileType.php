<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/ProfileType.php

namespace App\Form\Type;

use App\Form\Type\Field\FocusFieldType;
use App\Form\Type\Field\SkillFieldType;
use App\Entity\Volunteer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * 
 */
class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->addEventListener(
                        FormEvents::PRE_SET_DATA,
                        [$this, 'onPreSetData']
                )
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        // $user existence is determined in controller
        $user = $event->getData();
        $form = $event->getForm();
        if (get_class($user) === Volunteer::class) {
            $form
                    ->add('receiveEmail')
                    ->add('focuses', FocusFieldType::class)
                    ->add('skills', SkillFieldType::class)
            ;
        }
//        dd($user);
        // ...
    }

}
