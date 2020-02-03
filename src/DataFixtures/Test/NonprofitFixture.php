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
        $npo1->setWebsite('http://turkeysRUs.bogus.info');
        $npo1->addFocus($this->getReference('focus_health'));
        $this->setReference('nonprofit', $npo1);

        $npo3 = new Nonprofit();
        $npo3->setOrgname('Talk Trash Fund');
        $npo3->setEin('978654321');
        $npo3->setActive(true);
        $npo3->setWebsite('http://ttrash.bogus.info');

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
        $password2 = $this->encoder->encodePassword($staff1, '123Abc');
        $staff1->setPassword($password2);
        $staff1->setRoles(['ROLE_STAFF']);
        $this->setReference('staff', $staff1);

        $staff2 = new Staff();
        $staff2->setEmail('rather@bogus.info');
        $staff2->setEnabled(true);
        $staff2->setFname('Rather');
        $staff2->setSname('Bogus');
        $password1 = $this->encoder->encodePassword($staff2, '123Abc');
        $staff2->setPassword($password1);
        $staff2->setRoles(['ROLE_STAFF']);

        $npo->setStaff($staff);
        $manager->persist($staff);
        $manager->persist($npo);

        $opp = new Opportunity();
        $opp->setNonprofit($npo1);
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
        
        $npo1->addOpportunity($opp);
        $npo3->addOpportunity($opp1);
        
        $manager->persist($opp);
        $manager->persist($opp1);
        $npo1->setStaff($staff1);
        $npo3->setStaff($staff2);
        $manager->persist($staff1);
        $manager->persist($npo1);
        $manager->persist($npo3);
        
        $manager->flush();
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
