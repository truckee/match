<?php

namespace App\DataFixtures;

use App\Entity\Volunteer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $volunteer->setEnabled(true);
        $volunteer->setFname('Random');
        $volunteer->setSname('Bogus');
        $volunteer->setReceiveEmail(true);
        $password = $this->encoder->encodePassword($volunteer, '123Abc');
        $volunteer->setPassword($password);
        $volunteer->setRoles(['ROLE_VOLUNTEER']);
        $expires = new \DateTime();
        $volunteer->setTokenExpiresAt($expires->add(new \DateInterval('PT3H')));
        $manager->persist($volunteer);

        $manager->flush();
    }
}
