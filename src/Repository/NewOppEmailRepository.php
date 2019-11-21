<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Repository/NewOppEmailRepository.php

namespace App\Repository;

use App\Entity\NewOppEmail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NewOppEmail|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewOppEmail|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewOppEmail[]    findAll()
 * @method NewOppEmail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewOppEmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewOppEmail::class);
    }

    // /**
    //  * @return NewOppEmail[] Returns an array of NewOppEmail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NewOppEmail
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
