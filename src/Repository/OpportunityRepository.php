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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Opportunity::class);
    }

    public function getAllOpenOpps()
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('o')
                        ->select('o')
                        ->join('o.nonprofit', 'n')
                        ->where('o.expiredate > :now')
                        ->andWhere('n.active = true')
                        ->orderBy('n.orgname', 'ASC')
                        ->addOrderBy('o.oppname', 'ASC')
                        ->setParameter('now', $now)
                        ->getQuery()->getResult()
        ;
    }

    public function getOppsByFocusOrSkill($focuses, $skills)
    {
        $now = new \DateTime();
        $opps = $this->createQueryBuilder('o')
                        ->select('o, n.jsonFocus, o.jsonSkill')
                        ->join('o.nonprofit', 'n')
                        ->where('o.expiredate > :now')
                        ->andWhere('n.active = true')
                        ->orderBy('n.orgname', 'ASC')
                        ->addOrderBy('o.oppname', 'ASC')
                        ->setParameter('now', $now)
                        ->getQuery()->getResult()
        ;
        $matches = [];
        foreach ($opps as $item) {
            if (!empty(array_intersect($item['jsonFocus'], $focuses)) ||
                    !empty(array_intersect($item['jsonSkill'], $skill))) {
                array_push($matches, $item[0]);
            }
        }
        
        return $matches;
    }

}
