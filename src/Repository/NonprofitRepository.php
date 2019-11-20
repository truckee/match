<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Repository/NonprofitRepository.php

namespace App\Repository;

use App\Entity\Nonprofit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Nonprofit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nonprofit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nonprofit[]    findAll()
 * @method Nonprofit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NonprofitRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Nonprofit::class);
    }

    public function getActiveOpps()
    {
        $now = new \DateTime();
        
        return $this->createQueryBuilder('n')
                ->select('n.opportunites', 'o')
                ->where('o.expiredate > :now')
                ->orderBy('o.expiredate')
                ->setParameter('now', $now)
                ->getQuery()->getResult();
    }
    
    // /**
    //  * @return Nonprofit[] Returns an array of Nonprofit objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('o')
      ->andWhere('o.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('o.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Nonprofit
      {
      return $this->createQueryBuilder('o')
      ->andWhere('o.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
