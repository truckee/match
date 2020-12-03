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

}
