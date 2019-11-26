<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/OpportunityFixture.php

namespace App\DataFixtures;

use App\Entity\Opportunity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * 
 */
class OpportunityFixture extends Fixture implements OrderedFixtureInterface
{
    public const OPPORTUNITY_REFERENCE = 'opportunity';
    
    public function load(ObjectManager $manager)
    {
        $opp = new Opportunity();
        $opp->setNonprofit($this->getReference(NonprofitFixture::NONPROFIT_TURKEY_REFERENCE));
        $opp->setActive(true);
        $opp->setOppname('Feeder');
        $opp->setDescription('Get them to eat');
        $opp->addSkill($this->getReference(OptionsFixture::ADMIN_SKILL_REFERENCE));
        $this->addReference(self::OPPORTUNITY_REFERENCE, $opp);
        $manager->persist($opp);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
    //put your code here
}
