<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/PersonService.php

namespace App\Services;

use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;

class PersonService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // return name of appropriate CrudController
    public function roleConverter($id)
    {
        $person = $this->em->getRepository(Person::class)->findOneBy(['id' => $id]);
        if ($person->hasRole('ROLE_ADMIN') || $person->hasRole('ROLE_SUPER_ADMIN')) {
            return 'Admin';
        }
        if ($person->hasRole('ROLE_REP')) {
            return 'Representative';
        }
        if ($person->hasRole('ROLE_VOLUNTEER')) {
            return 'Volunteer';
        }
    }

    public function switchFns($class, $id, $field)
    {
        switch ($class):
            case 'Admin':
                if ('mailer' === $field) {
                    $flash = $this->mailer($id);
                }
                if ('enabled' === $field) {
                    $flash = $this->adminEnabler($id);
                }
                break;
            default:
                $flash = $this->enabler($class, $field, $id);
                break;
        endswitch;

        return $flash;
    }

    public function lockUser($id)
    {
        if (null === $id) {
            return ['type' => 'info', 'content' => 'No change made'];
        }

        $user = $this->em->getRepository(Person::class)->find($id);
        $state = $user->getLocked();
        $user->setLocked(!$state);
        $status = $user->getReplacementStatus();
        $this->em->persist($user);

        if ($user->hasRole('ROLE_REP') && ('Replace' === $status || 'Replacement' === $status)) {
            $nonprofit = $user->getNonprofit();
            $nonprofit->setActive(false);
            $this->em->persist($nonprofit);
        }
        $this->em->flush();
        $lockState = $user->getLocked() ? ' is now locked' : ' is now unlocked';

        return ['type' => 'success', 'content' => $user->getFullName() . $lockState];
    }

    private function mailer($id)
    {
        $mailer = $this->em->getRepository(Person::class)->findOneBy(['mailer' => true]);

        if ((int) $id === $mailer->getId()) {
            return ['type' => 'info', 'content' => 'No change made'];
        }

        $selected = $this->em->getRepository(Person::class)->find($id);
        if (false === $selected->getEnabled()) {

            return ['type' => 'warning', 'content' => 'Disabled admins cannot be mailer'];
        }

        $entities = $this->em->getRepository(Person::class)->findBy(['enabled' => true]);
        foreach ($entities as $admin) {
            if ((int) $id === $admin->getId() && false === $admin->getMailer()) {
                $admin->setMailer(true);
            } else {
                $admin->setMailer(false);
            }
            $this->em->persist($admin);
        }
        $this->em->flush();

        return ['type' => 'success', 'content' => 'Mailer status changed'];
    }

    private function enabler($class, $field, $id)
    {
        $persons = ['Admin', 'Representative', 'Volunteer'];
        if (in_array($class, $persons)) {
            $class = 'Person';
        }
        $entity = 'App\\Entity\\' . $class;
        $target = $this->em->getRepository($entity)->find($id);
        $getter = 'get' . ucfirst($field);
        $setter = 'set' . ucfirst($field);
        $value = $target->$getter();
        $target->$setter(!$value);
        $this->em->persist($target);
        $this->em->flush();

        return ['type' => 'success', 'content' => ucfirst($field) . ' status changed'];
    }

    private function adminEnabler($id)
    {
        $admin = $this->em->getRepository(Person::class)->find($id);
        $enabled = $admin->getEnabled();
        if (!$admin->getMailer() && !$admin->hasRole('ROLE_SUPER_ADMIN')) {
            $admin->setEnabled(!$enabled);
            $this->em->persist($admin);
            $this->em->flush();

            return ['type' => 'success', 'content' => 'Enabled status changed'];
        } else {

            return ['type' => 'danger', 'content' => $admin->getFullName() . ' cannot be disabled'];
        }
    }

}
