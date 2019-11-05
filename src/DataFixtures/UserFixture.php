<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Volunteer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * These users are for testing volunteer registration as well as all user
 * forgotten and reset password functions.
 *
 *  random@bogus.info: has completed initial registration form;
 *      has unexpired confirmation token, enabled false
 * pseudo@bogus.info: same. but enabled true
 *
 */
class UserFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // user with a 10 year confirmation token for testing
        // unexpired confirmation token; enabled = false
        $volunteer = new Volunteer();
        $volunteer->setConfirmationToken('abcdef');
        $volunteer->setEmail('random@bogus.info');
        // enabled false means not yet confirmed
        $volunteer->setEnabled(false);
        $volunteer->setFname('Random');
        $volunteer->setSname('Bogus');
        $volunteer->setReceiveEmail(true);
        $password = $this->encoder->encodePassword($volunteer, '123Abc');
        $volunteer->setPassword($password);
        $volunteer->setRoles(['ROLE_VOLUNTEER']);
        $expires = new \DateTime();
        $volunteer->setTokenExpiresAt($expires->add(new \DateInterval('P10Y')));
        $manager->persist($volunteer);
        
        // user enabled = true: for reset password, profile check
        $volunteer1 = new Volunteer();
        $volunteer1->setConfirmationToken('ghijkl');
        $volunteer1->setEmail('pseudo@bogus.info');
        $volunteer1->setEnabled(true);
        $volunteer1->setFname('Very');
        $volunteer1->setSname('Bogus');
        $volunteer1->setReceiveEmail(true);
        $password1 = $this->encoder->encodePassword($volunteer1, '123Abc');
        $volunteer1->setPassword($password1);
        $volunteer1->setRoles(['ROLE_VOLUNTEER']);
        $expires1 = new \DateTime();
        $volunteer1->setTokenExpiresAt($expires1->add(new \DateInterval('P10Y')));
        $manager->persist($volunteer1);
        
        // user enabled = true: for reset password, profile check
        $volunteer2 = new Volunteer();
        $volunteer2->setConfirmationToken('fedcba');
        $volunteer2->setEmail('garbled@bogus.info');
        $volunteer2->setEnabled(false);
        $volunteer2->setFname('Very');
        $volunteer2->setSname('Bogus');
        $volunteer2->setReceiveEmail(true);
        $password2 = $this->encoder->encodePassword($volunteer2, '123Abc');
        $volunteer2->setPassword($password2);
        $volunteer2->setRoles(['ROLE_VOLUNTEER']);
        $expires2 = new \DateTime();
        $volunteer2->setTokenExpiresAt($expires2->sub(new \DateInterval('PT3H')));
        $manager->persist($volunteer2);
        
        // admin user; also for testing null tokens
        $admin = new Admin();
        $admin->setEmail('admin@bogus.info');
        $admin->setEnabled(true);
        $admin->setFname('Benny');
        $admin->setSname('Borko');
        $admin->setRoles(['ROLE_ADMIN']);
        $password3 = $this->encoder->encodePassword($admin, '123Abc');
        $admin->setPassword($password3);
        $manager->persist($admin);
                
        $manager->flush();
    }
}
