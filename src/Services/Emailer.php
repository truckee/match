<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/Emailer.php

namespace App\Services;

//use \Swift_Mailer;

/**
 * 
 */
class Emailer
{
    private $mailer;
    private $sender;
    
    public function __construct(\Swift_Mailer $mailer, $senderAddress)
    {
        $this->mailer = $mailer;
        $this->sender = $senderAddress;
    }
    
    public function registrationMailer($mailParams)
    {
            $message = (new \Swift_Message($mailParams['subject']))
                    ->setFrom($this->sender)
                    ->setTo($mailParams['recipient'])
                    ->setBody(
                    $mailParams['view'],
                    'text/html'
                    )
            ;
            $this->mailer->send($message);
    }
}
