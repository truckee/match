<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/Field/FocusFieldType.php

namespace App\Form\Type\Field;

use App\Entity\Focus;
use App\Repository\FocusRepository as Repo;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 
 */
class FocusFieldType extends AbstractType
{
    private $repo;
    
    public function __construct(Repo $repo) {
        $this->repo = $repo;
    }
    
    public function getName()
    {
        return 'focuses';
    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => Focus::class,
                'choice_label' => 'focus',
                'expanded' => true,
                'multiple' => true,
                'attr' => array('class' => 'list-inline'),
                'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('f')
                        ->orderBy('f.focus', 'ASC')
                        ->where("f.enabled = '1'")
                        ->andWhere("f.focus <> 'All'");
            },
                'label' => $this->isPopulated(),
            )
        )
        ;
    }

    private function isPopulated()
    {
        $populated = $this->repo->findAll();

        return (0 === count($populated)) ? 'Sign in as Admin; add focus critieria' : 'Focus criteria';
    }
}
