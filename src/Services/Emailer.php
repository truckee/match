<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/Emailer.php

namespace App\Services;

use Symfony\Component\Dotenv\Dotenv;

/**
 * 
 */
class Emailer
{

    private $defaultMailer;
    private $spoolMailer;
    private $sender;
    private $projectDir;

    public function __construct($defaultMailer, $spoolMailer, $senderAddress, $projectDir)
    {
        $this->defaultMailer = $defaultMailer;
        $this->spoolMailer = $spoolMailer;
        $this->sender = $senderAddress;
        $this->projectDir = $projectDir;
    }

    public function appMailer($mailParams)
    {
        if (null === $mailParams['recipient']) {
            $dotenv = new Dotenv($usePutenv = false);
            $dotenv->load($this->projectDir . '/.env.local');
            $mailParams['recipient'] = $_ENV['NPO_ACTIVATOR'];
        }

        $message = (new \Swift_Message($mailParams['subject']))
                ->setFrom($this->sender)
                ->setTo($mailParams['recipient'])
                ->setBody(
                $mailParams['view'],
                'text/html'
                )
        ;

        if (!array_key_exists('spool', $mailParams)) {
            $this->defaultMailer->send($message);
        } else {
            $this->spoolMailer->send($message);
        }
    }

}

//// custom function to send an email
//// inject \Swift_Mailer like you normally would
//public function sendMessage($name, \Swift_Mailer $mailer, $bypassSpool = false)
//{
//    $message = new \Swift_Message('Hello Email')
//        ->setFrom(/* from */)
//        ->setTo(/* to */)
//        ->setBody(/* render view */);
//
//    $mailer->send($message); // pushes the message to the spool queue
//
//    if($bypassSpool) {
//        $spool = $mailer->getTransport->getSpool()
//        $spool->flushQueue(new Swift_SmtpTransport(
//            /* Get host, username and password from config */
//        ));
//    }
//}
 