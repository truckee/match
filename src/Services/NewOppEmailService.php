<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/NewOppEmailService.php

namespace App\Services;

use App\Entity\NewOppEmail;
use Doctrine\ORM\EntityManagerInterface;

/**
 * 
 */
class NewOppEmailService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * $volunteers: array of volunteer ids
     * $opportunity: id of new opportunity
     */
    public function addToList($volunteers, $opportunity)
    {
        $list = $this->em->getRepository(NewOppEmail::class)->findOneBy(['sent' => null]) ?? new NewOppEmail();
        $email = json_decode($list->getVolunteerEmail(), true);

        $email[$opportunity] = $focus;

        $list->setNVolunteers(count($focus) + $list->getNVolunteers());
        $list->setNOpportunities($list->getNOpportunities() + 1);
        $list->setVolunteerEmail(json_encode($email));
        $this->em->persist($list);

        $this->em->flush();
    }

}
