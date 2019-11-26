<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/OpportunityFixture.php

namespace App\DataFixtures\ORM;

use App\Entity\Opportunity;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;

/**
 * 
 */
class OpportunityFixture extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $opp = new Opportunity();
        $opp->setNonprofit($this->getReference('nonprofit'));
        $opp->setActive(true);
        $opp->setOppname('Feeder');
        $opp->setDescription('Get them to eat');
        $opp->addSkill($this->getReference('skill_admin'));
        $this->setReference('opportunity', $opp);
        $manager->persist($opp);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
    //put your code here
}
