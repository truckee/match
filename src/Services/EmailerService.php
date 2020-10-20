<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/Emailer.php

namespace App\Services;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;

class EmailerService
{
    private $defaultMailer;
    private $em;

    public function __construct(EntityManagerInterface $em, $defaultMailer)
    {
        $this->defaultMailer = $defaultMailer;
        $this->em = $em;
    }

    public function appMailer($mailParams)
    {
        $mailer = $this->em->getRepository(Admin::class)->findOneBy(['mailer' => true]);
        // used by new nonprofit notice, expired invitation, opportunities email report
        if (!array_key_exists('recipient', $mailParams)) {
            $mailParams['recipient'] = $mailer;
        }

        $message = (new \Swift_Message($mailParams['subject']))
                ->setFrom($mailer)
                ->setTo($mailParams['recipient'])
                ->setBody(
                    $mailParams['view'],
                    'text/html'
                )
        ;

        $this->defaultMailer->send($message);

        return true;
//        if (!array_key_exists('spool', $mailParams)) {
//            $this->defaultMailer->send($message);
//        } else {
//            $this->spoolMailer->send($message);
//        }
    }
}
