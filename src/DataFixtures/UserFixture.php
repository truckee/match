<?php

namespace App\DataFixtures;

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
        $volunteer = new Volunteer();
        $volunteer->setConfirmationToken('abcdef');
        $volunteer->setEmail('random@bogus.info');
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
        
        $volunteer1 = new Volunteer();
        $volunteer1->setConfirmationToken('ghijkl');
        $volunteer1->setEmail('pseudo@bogus.info');
        $volunteer1->setEnabled(true);
        $volunteer1->setFname('Very');
        $volunteer1->setSname('Bogus');
        $volunteer1->setReceiveEmail(true);
        $password = $this->encoder->encodePassword($volunteer1, '123Abc');
        $volunteer1->setPassword($password);
        $volunteer1->setRoles(['ROLE_VOLUNTEER']);
        $expires = new \DateTime();
        $volunteer1->setTokenExpiresAt($expires->add(new \DateInterval('P10Y')));
        $manager->persist($volunteer1);

        $manager->flush();
    }
}
