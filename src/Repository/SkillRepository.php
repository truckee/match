<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Repository/SkillRepository.php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * 
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    public function countSkills()
    {
        $em = $this->getEntityManager();

        return $em->createQuery('select count(s) from App:Focus s '
                    . "WHERE s.enabled = true")
                ->getSingleScalarResult();
    }
}
