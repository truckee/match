<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/NonprofitFixture.php

namespace App\DataFixtures;

use App\Entity\Nonprofit;
use App\Entity\Staff;
use App\DataFixtures\OptionsFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NonprofitFixture extends Fixture implements OrderedFixtureInterface
{
    public const NONPROFIT_TURKEY_REFERENCE = "npo1-turkey";

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
        $npo1->addFocus($this->getReference(OptionsFixture::HEALTH_FOCUS_REFERENCE));
        $this->addReference(self::NONPROFIT_TURKEY_REFERENCE, $npo1);

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
        $staff1->setEmail('staff@bogus.info');
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

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }

}
