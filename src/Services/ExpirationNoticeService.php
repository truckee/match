<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/ExpirationNoticeService.php

namespace App\Services;

use App\Entity\Opportunity;
use App\Services\EmailerService;
use Doctrine\ORM\EntityManagerInterface;

class ExpirationNoticeService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function expirationNotices(EmailerService $mailer)
    {
        $opps = $this->em->getRepository(Opportunity::class)->getExpiringOpps();
        if (empty($opps)) {
            return;
        }
        foreach ($opps as $item) {
            $staff = $item->getNonprofit()->getStaff();
            $view = 'Email/expiration_notice.html.twig';
            $context = ['opp' => $item, 'staff' => $staff,];
            $mailParams = [
                'view' => $view,
                'context' => $context,
                'recipient' => $staff->getEmail(),
                'subject' => 'Expiring opportunity',
            ];
            $mailer->appMailer($mailParams);
        }
    }
}
