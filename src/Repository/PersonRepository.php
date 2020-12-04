<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Repository/UserRepository.php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\Persistence\ManagerRegistry;

class PersonRepository extends ServiceEntityRepository implements UserLoaderInterface
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function loadUserByUsername(string $usernameOrEmail)
    {
        $em = $this->getEntityManager();

        return $em->createQuery(
                                'SELECT p
                FROM App\Entity\Person p
                WHERE p.email = :query'
                        )
                        ->setParameter('query', $usernameOrEmail)
                        ->getOneOrNullResult();
    }

    public function getLockableStaff($npo)
    {
        $qb = $this->createQueryBuilder('p');

        return $this->createQueryBuilder('p')
                        ->select('p')
//                        ->from(Person::class, 'p')
                        ->join('p.nonprofit', 'n')
                        ->where('n = ?1')
                        ->andWhere($qb->expr()->orX(
                                        $qb->expr()->eq('p.replacementStatus', '?2'),
                                        $qb->expr()->eq('p.replacementStatus', '?3'),
                        ))
                        ->setParameter(1, $npo)
                        ->setParameter(2, 'Pending')
                        ->setParameter(3, 'Replace')
                        ->getQuery()->getSingleResult();
    }

    public function opportunityEmails($opportunity)
    {
        $conn = $this->getEntityManager()->getConnection();
        $npoId = $opportunity->getNonprofit()->getId();
        $oppId = $opportunity->getId();

        // focus selected volunteers
        $focusSQL = 'SELECT p.id '
                . 'FROM person p '
                . 'JOIN vol_focus vf ON vf.volid = p.id '
                . 'JOIN org_focus org_f ON org_f.focusid = vf.focusid '
                . 'WHERE org_f.orgId = :npoId'
        ;
        $focusStmt = $conn->prepare($focusSQL);
        $focusStmt->execute(['npoId' => $npoId]);
        $focusVolunteers = $focusStmt->fetchAll();
        foreach ($focusVolunteers as $item) {
            $volunteers[] = $item['id'];
        }
        // skill selected volunteers
        $skillSQL = 'SELECT p.id FROM person p '
                . 'JOIN vol_skill vs on vs.volId = p.id '
                . 'JOIN opp_skill os ON os.skillId = vs.skillId '
                . 'WHERE os.oppId = :oppId';
        $skillStmt = $conn->prepare($skillSQL);
        $skillStmt->execute(['oppId' => $oppId]);
        $skillVolunteers = $skillStmt->fetchAll();
        ;
        foreach ($skillVolunteers as $item) {
            if (!in_array($item['id'], $volunteers)) {
                $volunteers[] = $item['id'];
            }
        }

        return $volunteers;
    }

}
