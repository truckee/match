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
use Symfony\Component\Validator\Constraints\Count;

/**
 *
 */
class FocusFieldType extends AbstractType
{
    private $repo;
    
    public function __construct(Repo $repo)
    {
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
                'constraints' => [new Count(['min' => 1, 'minMessage' => 'At least one focus please'])],
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

        return (0 === count($populated)) ? 'Sign in as Admin; add focus options' : 'Focus options';
    }
}
