<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/Emailer.php

namespace App\Services;

//use App\Entity\Person;
use Symfony\Component\Mailer\MailerInterface;
//use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailerService
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function appMailer($mailParams)
    {
        $email = (new TemplatedEmail())
                ->to($mailParams['recipient'])
                ->subject($mailParams['subject'])
                ->htmlTemplate($mailParams['template'])
                ->context($mailParams['context'])
        ;

        $this->mailer->send($email);

        return $email;
// For SwiftMailer
//        $message = (new \Swift_Message($mailParams['subject']))
//                ->setFrom($sender)
//                ->setTo($mailParams['recipient'])
//                ->setBody(
//                $mailParams['view'],
//                'text/html'
//                )
//        ;
//
//        $sent = $this->defaultMailer->send($message);
//
//        return $sent;
    }

//
//    public function getSender()
//    {
//        $sender = $this->em->getRepository(Person::class)->findOneBy(['mailer' => true]);
//
//        return $sender->getEmail();
//    }
}
