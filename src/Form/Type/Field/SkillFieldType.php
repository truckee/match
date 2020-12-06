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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

/**
 *
 */
class SkillFieldType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
                    'constraints' => [new Count(['min' => 1, 'minMessage' => 'At least one skill please'])],
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
        $populated = $this->em->getRepository(Skill::class)->findAll();

        return (0 === $populated) ? 'Sign in as Admin; add skill options' : false;
    }
}
