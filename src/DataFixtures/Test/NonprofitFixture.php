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
        $npo1 = new Nonprofit();
        $npo1->setOrgname('Marmot Fund');
        $npo1->setEin('123456789');
        $npo1->setActive(false);
        $this->setReference('marmot', $npo1);

        $npo2 = new Nonprofit();
        $npo2->setOrgname('Turkey Fund');
        $npo2->setEin('321654978');
        $npo2->setActive(true);
        $npo2->setWebsite('http://turkeysRUs.bogus.info');
        $npo2->addFocus($this->getReference('focus_health'));
        $this->setReference('nonprofit', $npo2);

        $npo3 = new Nonprofit();
        $npo3->setOrgname('Talk Trash Fund');
        $npo3->setEin('978654321');
        $npo3->setActive(true);
        $npo3->setWebsite('http://ttrash.bogus.info');

        $staff1 = new Staff();
        $staff1->setConfirmationToken('tuvxyz');
        $staff1->setEmail('unknown@bogus.info');
        $staff1->setEnabled(false);
        $staff1->setFname('Unknown');
        $staff1->setSname('Bogus');
        $password = $this->encoder->encodePassword($staff1, '123Abc');
        $staff1->setPassword($password);
        $staff1->setRoles(['ROLE_STAFF']);
        $expires = new \DateTime();
        $staff1->setTokenExpiresAt($expires->add(new \DateInterval('P10Y')));
        $staff1->setNonprofit($npo1);

        $staff2 = new Staff();
        $staff2->setEmail('staff@bogus.info');
        $staff2->setEnabled(true);
        $staff2->setFname('Misfit');
        $staff2->setSname('Bogus');
        $password2 = $this->encoder->encodePassword($staff2, '123Abc');
        $staff2->setPassword($password2);
        $staff2->setRoles(['ROLE_STAFF']);
        $this->setReference('staff', $staff2);
        $staff2->setNonprofit($npo2);

        $staff3 = new Staff();
        $staff3->setEmail('rather@bogus.info');
        $staff3->setEnabled(true);
        $staff3->setFname('Rather');
        $staff3->setSname('Bogus');
        $password1 = $this->encoder->encodePassword($staff3, '123Abc');
        $staff3->setPassword($password1);
        $staff3->setRoles(['ROLE_STAFF']);
        $staff3->setNonprofit($npo3);

//        $npo1->setStaff($staff1);
        $manager->persist($staff1);
        $manager->persist($npo1);

        $opp = new Opportunity();
        $opp->setNonprofit($npo2);
        $opp->setActive(true);
        $opp->setOppname('Feeder');
        $opp->setDescription('Lorem ipsum dolor sit amet, consectetuer adipiscing '
                . 'elit. Maecenas porttitor congue massa. Fusce posuere, magna '
                . 'sed pulvinar ultricies, purus lectus malesuada libero, sit '
                . 'amet commodo magna eros quis urna.');
        $opp->addSkill($this->getReference('skill_admin'));
        $this->setReference('opp', $opp);

        $opp1 = new Opportunity();
        $opp1->setNonprofit($npo3);
        $opp1->setActive(true);
        $opp1->setOppname('Talker');
        $opp1->setBackground(false);
        $opp1->setGroupOk(true);
        $opp1->setMinage('18');
        $opp1->setDescription('Fusce aliquet pede non pede. Suspendisse dapibus'
                . ' lorem pellentesque magna. Integer nulla. Donec blandit '
                . 'feugiat ligula. Donec hendrerit, felis et imperdiet euismod, '
                . 'purus ipsum pretium metus, in lacinia nulla nisl eget sapien.');
        $opp1->addSkill($this->getReference('skill_admin'));
        
        $npo2->addOpportunity($opp);
        $npo3->addOpportunity($opp1);
        
        $manager->persist($opp);
        $manager->persist($opp1);
//        $npo2->setStaff($staff2);
//        $npo3->setStaff($staff3);
        $manager->persist($staff2);
        $manager->persist($staff3);
        $manager->persist($npo1);
        $manager->persist($npo2);
        $manager->persist($npo3);
        
        $manager->flush();
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }

}
