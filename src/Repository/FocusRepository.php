<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Repository/FocusRepository.php

namespace App\Repository;

use App\Entity\Focus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * 
 */
class FocusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Focus::class);
    }

    public function countFocuses()
    {
        $em = $this->getEntityManager();

        return $em->createQuery('select count(f) from App:Focus f '
                    . "WHERE f.enabled = true")
                ->getSingleScalarResult();
    }
}
