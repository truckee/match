<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Repository/VolunteerRepository.php

namespace App\Repository;

use App\Entity\Volunteer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class VolunteerRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volunteer::class);
    }

    public function opportunityEmails($opportunity)
    {
        $nonprofit = $opportunity->getNonprofit();
        $orgFocuses = $nonprofit->getJsonFocus();
        $oppSkills = $opportunity->getJsonSkill();
        
        // get elibible volunteers
        $qb = $this->createQueryBuilder('v')
                ->select('v')
                ->where('v.receiveEmail = true')
                ->andWhere('v.enabled = true')
                ->getQuery()->getResult();
        $volunteers = [];
        foreach ($qb as $entity) {
            $id = $entity->getId();
            if (!empty(array_intersect($orgFocuses, $entity->getJsonFocus()))) {
                array_push($volunteers, $id);
            }
            if (!empty(array_intersect($oppSkills, $entity->getJsonSkill())) &&
                    !in_array($id, $volunteers)) {
                array_push($volunteers, $id);
            }
        }
//        foreach ($volunteers as $id) {
//            $result[] = ['id'=>$id];
//        }
//dd($result);
        return $volunteers;
    }

}
