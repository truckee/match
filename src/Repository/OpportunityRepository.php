<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Repository/OpportunityRepository.php

namespace App\Repository;

use App\Entity\Opportunity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * 
 */
class OpportunityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Opportunity::class);
    }
    
    public function getSkillIds($opp)
    {
        $skills = $opp->getSkills();
        $ids = [];
        foreach ($skills as $skill) {
           $ids[] = $skill->getId();
        }

        return $ids;    }
}
