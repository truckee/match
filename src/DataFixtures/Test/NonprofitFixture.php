<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/NonprofitFixture.php

namespace App\DataFixtures\Test;

use App\Entity\Nonprofit;
use App\Entity\Opportunity;
use App\Entity\Staff;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;

class NonprofitFixture extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
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
        $this->setReference('marmot', $npo);

        $npo1 = new Nonprofit();
        $npo1->setOrgname('Turkey Fund');
        $npo1->setEin('321654978');
        $npo1->setActive(true);
        $npo1->addFocus($this->getReference('focus_health'));
        $this->setReference('nonprofit', $npo1);

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
        $password1 = $this->encoder->encodePassword($staff1, '123Abc');
        $staff1->setPassword($password1);
        $staff1->setRoles(['ROLE_STAFF']);
        $this->setReference('staff', $staff1);

        $npo->setStaff($staff);
        $manager->persist($staff);
        $manager->persist($npo);

        $opp = new Opportunity();
        $opp->setNonprofit($npo1);
        $opp->setActive(true);
        $opp->setOppname('Feeder');
        $opp->setDescription('Get them to eat');
        $opp->addSkill($this->getReference('skill_admin'));
        $this->setReference('opp', $opp);
        
        $npo1->addOpportunity($opp);
        
        $manager->persist($opp);
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
