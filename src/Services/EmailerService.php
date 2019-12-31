<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/Emailer.php

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailerService
{
    private $mailer;
    private $sender;
    private $activater;

    public function __construct($mailer, $senderAddress, $npoActivater)
    {
        $this->mailer = $mailer;
        $this->sender = $senderAddress;
        $this->activater = $npoActivater;
    }

    public function appMailer($mailParams)
    {
        if (null === $mailParams['recipient']) {
            $mailParams['recipient'] = $this->activater;
        }
        $email = (new TemplatedEmail())
                ->from($this->sender)
                ->to($mailParams['recipient'])
                ->subject($mailParams['subject'])
                ->htmlTemplate($mailParams['view'])
                ->context($mailParams['context']);
        
        $this->mailer->send($email);
        
        return $email;
    }
}
