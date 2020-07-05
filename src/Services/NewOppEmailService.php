<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/NewOppEmailService.php

namespace App\Services;

use App\Entity\Admin;
use App\Entity\NewOppEmail;
use App\Entity\Volunteer;
use App\Services\EmailerService;
use Doctrine\ORM\EntityManagerInterface;

class NewOppEmailService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Send new opportunity email to registered volunteers
     */
    public function newOppEmail(EmailerService $mailer, $volunteers, $opp)
    {
        $oppVol = [];
        foreach ($volunteers as $id) {
            $person = $this->em->getRepository(Volunteer::class)->find($id);
            $mailParams = [
                'view' => 'Email/volunteer_opportunities.html.twig',
                'context' => ['fname' => $person->getFname(), 'opportunity' => $opp,],
                'recipient' => $person->getEmail(),
                'subject' => 'New volunteer opportunity',
            ];
            $mailer->appMailer($mailParams);
            $oppVol[$opp->getId()][] = $id;
        }
        
        // send report to admin
        $nVolunteers = count($volunteers);
        $activator = $this->em->getRepository(Admin::class)->findOneBy(['activator' => true]);
        $mailParams = [
            'view'=>'Email/opportunity_email_report.html.twig',
            'context' => ['nVolunteers' => $nVolunteers, 'opportunity' => $opp,],
            'recipient'=> $activator,
            'subject'=>'Volunteer opportunities email report',
        ];
        $mailer->appMailer($mailParams);
        
        // update history
        $oppMail = new NewOppEmail();
        $oppMail->setDateAdded(new \DateTime());
        $oppMail->setNVolunteers($nVolunteers);
        $oppMail->setOpportunityEmail($oppVol);
        $this->em->persist($oppMail);
        $this->em->flush();
        
        return $oppVol;
    }
}
