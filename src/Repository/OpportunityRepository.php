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
use Doctrine\ORM\QueryBuilder;

/**
 * 
 */
class OpportunityRepository extends ServiceEntityRepository {

//    private $qb;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Opportunity::class);
    }

    private function opportunityQueryBuilder(QueryBuilder $qb) {

        return $qb
                        ->select('o')
                        ->join('o.nonprofit', 'n')
                        ->where('o.active = true')
                        ->andWhere('n.active = true')
                        ->orderBy('n.orgname', 'ASC')
                        ->addOrderBy('o.oppname', 'ASC')
        ;
    }

    public function getAllOpenOpps() {
        $now = new \DateTime();
        $qb = $this->createQueryBuilder('o');

        return $this->opportunityQueryBuilder($qb)
                        ->andWhere('o.expiredate > :now')
                        ->setParameter('now', $now)
                        ->getQuery()->getResult();
        ;
    }

    public function getOppsByFocusOrSkill($focuses, $skills) {
        $now = new \DateTime();
        $qb = $this->createQueryBuilder('o');

        $opps = $this->opportunityQueryBuilder($qb)
                        ->addSelect('o, n.jsonFocus, o.jsonSkill')
                        ->andWhere('o.expiredate > :now')
                        ->setParameter('now', $now)
                        ->getQuery()->getResult()
        ;
        $matches = [];
        foreach ($opps as $item) {
            if (!empty(array_intersect($item['jsonFocus'], $focuses)) ||
                    !empty(array_intersect($item['jsonSkill'], $skills))) {
                array_push($matches, $item[0]);
            }
        }

        return $matches;
    }

    public function getExpiringOpps() {
        $qb = $this->createQueryBuilder('o');
        $expiring = (new \DateTime())->add(new \DateInterval('P7D'))->settime(0,0);;

        return $this->opportunityQueryBuilder($qb)
                        ->andWhere('o.expiredate = :expiring')
                        ->setParameter('expiring', $expiring)
                        ->getQuery()->getResult();
    }

}
