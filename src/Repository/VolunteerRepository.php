<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Repository/VolunteerRepository.php

namespace App\Repository;

use App\Entity\Nonprofit;
use App\Entity\Opportunity;
use App\Entity\Volunteer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class VolunteerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Volunteer::class);
    }
    
    public function opportunityEmails($opportunity)
    {
        $nonprofit = $opportunity->getNonprofit();
        $focusIds = $this->getEntityManager()->getRepository(Nonprofit::class)->getFocusIds($nonprofit);
        $skillIds = $this->getEntityManager()->getRepository(Opportunity::class)->getSkillIds($opportunity);
//        dd($skillIds);
        $qb = $this->createQueryBuilder('v')
                ->select('v')
                ->where('v.skills IN(:os)')
                ->andWhere('v.focuses IN(:of)')
                ->setParameters([
                    'os'=>$skillIds,
                    'of'=>$focusIds
                        ])
                ->getQuery();
        $query = $qb->execute();
        
        dd($query);
    }
}
