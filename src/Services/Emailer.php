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

    private $mailer;
    private $sender;
    private $projectDir;

    public function __construct(\Swift_Mailer $mailer, $senderAddress, $projectDir)
    {
        $this->mailer = $mailer;
        $this->sender = $senderAddress;
        $this->projectDir = $projectDir;
    }

    public function appMailer($mailParams)
    {
        if (null === $mailParams['recipient']) {
            $dotenv = new Dotenv($usePutenv = false);
            $dotenv->load($this->projectDir.'/.env.local');
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
        $this->mailer->send($message);
    }

}
