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
        $conn  = $this->getEntityManager()->getConnection();
        $npoId = $opportunity->getNonprofit()->getId();
        $oppId = $opportunity->getId();
        
        // focus selected volunteers
        $focusSQL = 'SELECT v.id '
                . 'FROM volunteer v '
                . 'JOIN vol_focus vf ON vf.volid = v.id '
                . 'JOIN org_focus of ON of.focusid = vf.focusid '
                . 'WHERE of.orgId = :npoId'
                ;
        $focusStmt = $conn->prepare($focusSQL);
        $focusStmt->execute(['npoId' => $npoId]);
        $focusVolunteers = $focusStmt->fetchAll();
        foreach ($focusVolunteers as $item) {
            $volunteers[] = $item['id'];
        }
        
        // skill selected volunteers
        $skillSQL = 'SELECT v.id FROM volunteer v '
                . 'JOIN vol_skill vs on vs.volId = v.id '
                . 'JOIN opp_skill os ON os.skillId = vs.skillId '
                . 'WHERE os.oppId = :oppId';
        $skillStmt = $conn->prepare($skillSQL);
        $skillStmt->execute(['oppId' => $oppId]);
        $skillVolunteers = $skillStmt->fetchAll();
        foreach ($skillVolunteers as $item) {
            if (!in_array($item['id'], $volunteers)) {
                $volunteers[] = $item['id'];
            }
        }
        
        return $volunteers;
    }
}
