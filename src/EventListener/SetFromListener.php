<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/EventListener/SetFromListener.php

namespace App\EventListener;

use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class SetFromListener implements EventSubscriberInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }

    public function onMessage(MessageEvent $event)
    {
        $email = $event->getMessage();

        if (!$email instanceof Email) {
            return;
        }
        $from = $this->em->getRepository(Person::class)->findOneBy(['mailer' => true]);

        $email->from(new Address($from->getEmail(), 'Volunteer Connections'));
    }

}
