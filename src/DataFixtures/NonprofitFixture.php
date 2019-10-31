<?php

namespace App\DataFixtures;

use App\Entity\Nonprofit;
use App\Entity\Staff;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NonprofitFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $npo = new Nonprofit();
        $npo->setOrgname('Marmot Fund');
        $npo->setEin('123456789');

        // staff has expired confirmation token
        $staff = new Staff();
        $staff->setConfirmationToken('fedcba');
        $staff->setEmail('unknown@bogus.info');
        $staff->setEnabled(false);
        $staff->setFname('Unknown');
        $staff->setSname('Bogus');
        $password = $this->encoder->encodePassword($staff, '123Abc');
        $staff->setPassword($password);
        $staff->setRoles(['ROLE_STAFF']);
        $expires = new \DateTime();
        $staff->setTokenExpiresAt($expires->sub(new \DateInterval('PT3H')));

        $npo->setStaff($staff);
        $manager->persist($staff);
        $manager->persist($npo);

        $manager->flush();
    }

}
