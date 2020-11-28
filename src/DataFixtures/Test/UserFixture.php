<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/UserFixture.php

namespace App\DataFixtures\Test;

use App\Entity\Person;
//use App\Entity\Volunteer;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;

/**
 * These users are for testing volunteer registration as well as all user
 * forgotten and reset password functions.
 *
 *  random@bogus.info: has completed initial registration form;
 *      has unexpired confirmation token, enabled false
 * pseudo@bogus.info: same. but enabled true
 *
 */
class UserFixture extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // user random@bogus.info with a 10 year confirmation token for testing
        // unexpired confirmation token; enabled = false
        {
            $volunteer = new Person('ROLE_VOLUNTEER');
            $volunteer->setConfirmationToken('abcdef');
            $expires = new \DateTime();
            $volunteer->setTokenExpiresAt($expires->add(new \DateInterval('P10Y')));
            // enabled false means not yet confirmed
            $volunteer->setEnabled(false);

            $volunteer->setEmail('random@bogus.info');
            $volunteer->setFname('Random');
            $volunteer->setSname('Bogus');
            $volunteer->setReceiveEmail(true);
            $password = $this->encoder->encodePassword($volunteer, '123Abc');
            $volunteer->setPassword($password);
//            $volunteer->setRoles(['ROLE_VOLUNTEER']);
            $manager->persist($volunteer);
        }

        // user pseudo@bogus.info enabled = true:
        // for reset password, profile check
        {
            $volunteer1 = new Person('ROLE_VOLUNTEER');
            $volunteer1->setConfirmationToken('ghijkl');
            $volunteer1->setTokenExpiresAt($expires);
            $volunteer1->setEnabled(true);

            $volunteer1->setEmail('pseudo@bogus.info');
            $volunteer1->setFname('Very');
            $volunteer1->setSname('Bogus');
            $volunteer1->setReceiveEmail(true);
            $password1 = $this->encoder->encodePassword($volunteer1, '123Abc');
            $volunteer1->setPassword($password1);
            $manager->persist($volunteer1);
        }

        // user garbled@bogus.info with expired confirmation token
        {
            $volunteer2 = new Person('ROLE_VOLUNTEER');
            $volunteer2->setConfirmationToken('fedcba');
            $expires2 = new \DateTime();
            $volunteer2->setTokenExpiresAt($expires2->sub(new \DateInterval('PT3H')));
            $volunteer2->setEnabled(false);

            $volunteer2->setEmail('garbled@bogus.info');
            $volunteer2->setFname('Very');
            $volunteer2->setSname('Bogus');
            $volunteer2->setReceiveEmail(true);
            $password2 = $this->encoder->encodePassword($volunteer2, '123Abc');
            $volunteer2->setPassword($password2);
            $manager->persist($volunteer2);
        }

        // user volunteer@bogus.info for testing searches
        {
            $volunteer3 = new Person('ROLE_VOLUNTEER');
            $volunteer3->setEnabled(true);

            $volunteer3->setEmail('volunteer@bogus.info');
            $volunteer3->setFname('Exceptionally');
            $volunteer3->setSname('Bogus');
            $volunteer3->setReceiveEmail(true);
            $password3 = $this->encoder->encodePassword($volunteer3, '123Abc');
            $volunteer3->setPassword($password3);
            $volunteer3->addFocus($this->getReference('focus_seniors'));
            $volunteer3->addFocus($this->getReference('focus_health'));
            $volunteer3->addSkill($this->getReference('skill_admin'));
            $volunteer3->addSkill($this->getReference('skill_board'));
            $this->setReference('volunteer', $volunteer3);
            $manager->persist($volunteer3);
        }

        // admin user; also for testing null tokens
        {
            $admin = new Person('ROLE_SUPER_ADMIN');
            $admin->setEmail('admin@bogus.info');
            $admin->setEnabled(true);
            $admin->setMailer(false);
            $admin->setFname('Benny');
            $admin->setSname('Borko');
            $password4 = $this->encoder->encodePassword($admin, '123Abc');
            $admin->setPassword($password4);
            $manager->persist($admin);
        } {
            $admin1 = new Person('ROLE_ADMIN');
            $admin1->setConfirmationToken('mynameis');
            $admin1->setTokenExpiresAt($expires);
            $admin1->setEmail('obvious@bogus.info');
            $admin1->setEnabled(true);
            $admin1->setMailer(false);
            $admin1->setFname('Benny');
            $admin1->setSname('Borko');
            $admin1->setPassword($this->encoder->encodePassword($admin, '123Abc'));
            $manager->persist($admin1);
        } {
            $admin2 = new Person('ROLE_ADMIN');
            $admin2->setConfirmationToken('whoami');
            $admin2->setTokenExpiresAt($expires2);
            $admin2->setEmail('nothere@bogus.info');
            $admin2->setEnabled(false);
            $admin2->setMailer(false);
            $admin2->setFname('Benny');
            $admin2->setSname('Borko');
            $admin2->setPassword('mynameis');
            $manager->persist($admin2);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }

}
