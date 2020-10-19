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
use App\Entity\Representative;
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

        $rep1 = new Representative();
        $rep1->setConfirmationToken('tuvxyz');
        $rep1->setEmail('unknown@bogus.info');
        $rep1->setEnabled(false);
        $rep1->setFname('Unknown');
        $rep1->setSname('Bogus');
        $password = $this->encoder->encodePassword($rep1, '123Abc');
        $rep1->setPassword($password);
        $rep1->setRoles(['ROLE_REP']);
        $expires = new \DateTime();
        $rep1->setTokenExpiresAt($expires->add(new \DateInterval('P10Y')));
        $rep1->setNonprofit($npo1);
        $rep1->setReplacementStatus("Replace");

        $rep2 = new Representative();
        $rep2->setEmail('staff@bogus.info');
        $rep2->setEnabled(true);
        $rep2->setFname('Misfit');
        $rep2->setSname('Bogus');
        $password2 = $this->encoder->encodePassword($rep2, '123Abc');
        $rep2->setPassword($password2);
        $rep2->setRoles(['ROLE_REP']);
        $this->setReference('staff', $rep2);
        $rep2->setNonprofit($npo2);
        $rep2->setReplacementStatus("Replace");

        $rep3 = new Representative();
        $rep3->setEmail('rather@bogus.info');
        $rep3->setEnabled(true);
        $rep3->setFname('Rather');
        $rep3->setSname('Bogus');
        $password1 = $this->encoder->encodePassword($rep3, '123Abc');
        $rep3->setPassword($password1);
        $rep3->setRoles(['ROLE_REP']);
        $rep3->setNonprofit($npo3);
        $rep3->setReplacementStatus("Replace");

        $manager->persist($rep1);
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
        $manager->persist($rep2);
        $manager->persist($rep3);
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
