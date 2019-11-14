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
        $npo->setActive(false);

        $npo1 = new Nonprofit();
        $npo1->setOrgname('Turkey Fund');
        $npo1->setEin('321654978');
        $npo1->setActive(true);

        $staff = new Staff();
        $staff->setConfirmationToken('tuvxyz');
        $staff->setEmail('unknown@bogus.info');
        $staff->setEnabled(false);
        $staff->setFname('Unknown');
        $staff->setSname('Bogus');
        $password = $this->encoder->encodePassword($staff, '123Abc');
        $staff->setPassword($password);
        $staff->setRoles(['ROLE_STAFF']);
        $expires = new \DateTime();
        $staff->setTokenExpiresAt($expires->add(new \DateInterval('P10Y')));

        $staff1 = new Staff();
        $staff1->setEmail('backwards@bogus.info');
        $staff1->setEnabled(true);
        $staff1->setFname('Misfit');
        $staff1->setSname('Bogus');
        $password = $this->encoder->encodePassword($staff1, '123Abc');
        $staff1->setPassword($password);
        $staff1->setRoles(['ROLE_STAFF']);

        $npo->setStaff($staff);
        $manager->persist($staff);
        $manager->persist($npo);

        $npo1->setStaff($staff1);
        $manager->persist($staff1);
        $manager->persist($npo1);

        $manager->flush();
    }
}
