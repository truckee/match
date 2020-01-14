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
class OpportunityRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Opportunity::class);
    }

    private function opportunityQueryBuilder(QueryBuilder $qb)
    {
        return $qb
                        ->select('o')
                        ->join('o.nonprofit', 'n')
                        ->where('o.active = true')
                        ->andWhere('n.active = true')
                        ->orderBy('n.orgname', 'ASC')
                        ->addOrderBy('o.oppname', 'ASC')
        ;
    }

    public function getAllOpenOpps()
    {
        $now = new \DateTime();
        $qb = $this->createQueryBuilder('o');

        return $this->opportunityQueryBuilder($qb)
                        ->andWhere('o.expiredate > :now')
                        ->setParameter('now', $now)
                        ->getQuery()->getResult();
    }

    /**
     * Return opportunities, selected by focus and/or skill criteria
     * 
     * $focuses, $skills are arrays of ids
     */
    public function getOppsByFocusOrSkill($focuses, $skills)
    {
        $conn = $this->getEntityManager()->getConnection();
        $now = date('Y-m-d');
        $opps = [];

        // opps by skills
        if (!empty($skills)) {
            $skillSQL = 'SELECT o.id FROM opportunity o '
                    . 'JOIN nonprofit n ON n.id = o.orgId '
                    . 'JOIN opp_skill os ON os.oppId = o.id '
                    . 'WHERE n.active = true '
                    . 'AND o.expiredate > ' . $now . ' '
                    . 'AND os.skillId IN (?)';
            $skillStmt = $conn->executeQuery($skillSQL, 
                    [$skills], [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
            $skillOpps = $skillStmt->fetchAll();
            foreach ($skillOpps as $item) {
                $opps[] = $this->getEntityManager()->getRepository(Opportunity::class)->find($item['id']);
            }
        }
        
        // opps by focus
        if (!empty($focuses)) {
            $focusSQL = 'SELECT o.id FROM opportunity o '
                    . 'JOIN nonprofit n ON n.id = o.orgId '
                    . 'JOIN org_focus of ON of.orgId = n.id '
                    . 'WHERE n.active = true '
                    . 'AND o.expiredate > ' . $now . ' '
                    . 'AND of.focusId IN (?)'
            ;
            $focusStmt = $conn->executeQuery($focusSQL, 
                    [$focuses], [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]);
            $focusOpps = $focusStmt->fetchAll();
            foreach ($focusOpps as $item) {
                if (!in_array($item['id'], $focusOpps)) {
                    $opps[] = $this->getEntityManager()->getRepository(Opportunity::class)->find($item['id']);
                }
            }
        }
        
        return $opps;
    }

    public function getExpiringOpps()
    {
        $qb = $this->createQueryBuilder('o');
        $expiring = (new \DateTime())->add(new \DateInterval('P7D'))->settime(0, 0);

        return $this->opportunityQueryBuilder($qb)
                        ->andWhere('o.expiredate = :expiring')
                        ->setParameter('expiring', $expiring)
                        ->getQuery()->getResult();
    }
}
