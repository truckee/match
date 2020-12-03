<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/PersonService.php

namespace App\Services;

use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;

class PersonService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function roleConverter($id)
    {
        $person = $this->em->getRepository(Person::class)->findOneBy(['id' => $id]);
        if ($person->hasRole('ROLE_ADMIN')) {
            return 'Admin';
        }
        if ($person->hasRole('ROLE_REP')) {
            return 'Representative';
        }
        if ($person->hasRole('ROLE_VOLUNTEER')) {
            return 'Volunteer';
        }
    }

}
