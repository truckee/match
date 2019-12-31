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
use App\Entity\Opportunity;
use App\Entity\Volunteer;
use App\Services\EmailerService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

/**
 *
 */
class NewOppEmailService
{
    private $em;
    private $activater;

    public function __construct(EntityManagerInterface $em, $npoActivater)
    {
        $this->em = $em;
        $this->activater = $npoActivater;
    }

    /**
     * $volunteers: array of volunteer ids [id1, id2, ...]
     * $opportunity: id of new opportunity
     */
    public function addToList($volunteers, $oppid)
    {
        $list = $this->em->getRepository(NewOppEmail::class)->findOneBy(['sent' => null]) ?? new NewOppEmail();
        $volOpps = $list->getVolunteerEmail();
        // $volOpps = [ volunteer1_id1 =>[ opp1_id, ...], volunteer_id2  = [opp_id, ...] ...]
        $keys = array_keys($volOpps);
        foreach ($volunteers as $id) {
            if (!in_array($id, $keys)) {
                $volOpps[$id] = [];
            }
            array_push($volOpps[$id], $oppid);
        }

        $list->setNVolunteers(count($volOpps) + $list->getNVolunteers());
        $list->setNOpportunities($list->getNOpportunities() + 1);
        $list->setVolunteerEmail($volOpps);
        $this->em->persist($list);

        $this->em->flush();

        return $volOpps;
    }

    public function sendList(EmailerService $mailer, Environment $templating)
    {
        $list = $this->em->getRepository(NewOppEmail::class)->findOneBy(['sent' => false]);
        if (null === $list) {
            return;
        }
        $volOpps = $list->getVolunteerEmail();
        $keys = array_keys($volOpps);
        foreach ($keys as $volId) {
            $volunteer = $this->em->getRepository(Volunteer::class)->find($volId);
            $fname = $volunteer->getFname();
            $email = $volunteer->getEmail();
            foreach ($volOpps[$volId] as $oppId) {
                $opportunities[] = $this->em->getRepository(Opportunity::class)->find($oppId);
            }
            // email volunteer
            $view = 'Email/volunteer_opportunities.html.twig';
            $context = ['fname' => $fname, 'opportunities' => $opportunities,];
            $mailParams = [
                'view' => $view,
                'recipient' => $email,
                'subject' => 'Latest volunteer opportunities',
                'context' => $context,
                'spool' => true,
            ];
            $mailer->appMailer($mailParams);
        }
        
        // email admin on emails sent
        $countVolunteers = $list->getNVolunteers();
        $countOpportunities = $list->getNOpportunities();
        $view = 'Email/opportunity_email_report.html.twig';
        $context = ['nVolunteers' => $countVolunteers, 'nOpportunities' => $countOpportunities,];
        $mailParams = [
            'view'=>$view,
            'recipient'=> $this->activater,
            'subject'=>'Volunteer opportunities email report',
            'context' => $context,
        ];
        $mailer->appMailer($mailParams);
        
        $list->setSent(true);
        $this->em->persist($list);
        
        $this->em->flush();
    }
}
