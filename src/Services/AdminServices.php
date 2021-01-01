<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/AdminServices.php

namespace App\Services;

use App\Entity\Person;
use App\Services\EmailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class AdminServices
{

    private $em;
    private $emailSvc;
    private $session;
    private $twig;

    public function __construct(
            EntityManagerInterface $em,
            Environment $twig,
            SessionInterface $session,
            EmailerService $emailSvc)
    {
        $this->em = $em;
        $this->emailSvc = $emailSvc;
        $this->session = $session;
        $this->twig = $twig;
    }

    public function statusChange($npo)
    {
        $status = $npo->isActive();
        $npo->setActive(!$status);
        $rep = $this->em->getRepository(Person::class)->getLockableStaff($npo);
        // activate staff if $status is false
        if (false === $status) {
            $rep->setLocked(false);
            $rep->setEnabled(true);
            $mailParams = [
                'template' => 'Email/nonprofit_activated.html.twig',
                'recipient' => $rep->getEmail(),
                'subject' => 'Nonprofit activated!',
                'context' => [
                    'npo' => $npo,
                    'staff' => $rep,
                ]
            ];

            $this->emailSvc->appMailer($mailParams);
            $message = 'Nonprofit activated!';
        } else {
            $npo->setActive(false);
            $rep->setLocked(true);
            $message = 'Nonprofit deactivated; staff account locked';
        }
        $this->em->persist($npo);
        $this->em->persist($rep);
        $this->em->flush();

        return $message;
    }

}
