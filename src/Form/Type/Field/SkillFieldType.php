<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Form/Type/Field/SkillFieldType.php

namespace App\Form\Type\Field;

use App\Entity\Skill;
use App\Repository\SkillRepository as Repo;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * 
 */
class SkillFieldType extends AbstractType
{
    private $repo;
    
    public function __construct(Repo $repo) {
        $this->repo = $repo;
    }

    public function getName()
    {
        return 'skills';
    }

    public function getParent()
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => Skill::class,
                'choice_label' => 'skill',
                'expanded' => true,
                'multiple' => true,
                'attr' => array('class' => 'list-inline'),
                'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('s')
                        ->orderBy('s.skill', 'ASC')
                        ->where("s.enabled = '1'")
                        ->andWhere("s.skill <> 'All'");
            },
                'label' => $this->isPopulated(),
            )
        )
        ;
    }

    private function isPopulated()
    {
        $populated = $this->repo->findAll();

        return (0 === $populated) ? 'Sign in as Admin; add focus critieria' : 'Focus criteria';
    }
}
