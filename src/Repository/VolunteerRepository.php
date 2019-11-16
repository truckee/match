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
        $npoId = $nonprofit->getId();
        $oppId = $opportunity->getId();

        $conn = $this->getEntityManager()->getConnection();

        $sqlFocus = "SELECT DISTINCT(u.id), $oppId AS opp FROM usertable u
            JOIN vol_focus vf ON vf.volId = u.id
            JOIN org_focus of ON of.focusId = vf.focusId
            WHERE of.orgId = :id AND u.enabled = true AND u.receiveMail = true"
        ;
        $stmtFocus = $conn->prepare($sqlFocus);
        $stmtFocus->execute(['id' => $npoId]);
        $usersByFocus = $stmtFocus->fetchAll();

        $sqlSkill = "SELECT DISTINCT(u.id), $oppId AS opp FROM usertable u
            JOIN vol_skill vs ON vs.volId = u.id
            JOIN opp_skill os ON os.skillId = vs.skillId
            WHERE os.oppId = :id AND u.enabled = true AND u.receiveMail = true"
        ;
        $stmtSkill = $conn->prepare($sqlSkill);
        $stmtSkill->execute(['id' => $oppId]);
        $usersBySkill = $stmtSkill->fetchAll();
        
        return [$usersByFocus, $usersBySkill];
    }

}
