<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/Emailer.php

namespace App\Services;

/**
 *
 */
class EmailerService
{
    private $defaultMailer;
    private $spoolMailer;
    private $sender;
    private $projectDir;
    private $activator;

    public function __construct($defaultMailer, $spoolMailer, $senderAddress, $projectDir, $npoActivator)
    {
        $this->defaultMailer = $defaultMailer;
        $this->spoolMailer = $spoolMailer;
        $this->sender = $senderAddress;
        $this->projectDir = $projectDir;
        $this->activator = $npoActivator;
    }

    public function appMailer($mailParams)
    {
        if (!array_key_exists('recipient', $mailParams)) {
            $mailParams['recipient'] = $this->activator;
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
