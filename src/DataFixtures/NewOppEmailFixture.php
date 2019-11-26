<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/NewOppEmailFixture.php

namespace App\DataFixtures;

use App\Entity\NewOppEmail;
use App\DataFixtures\OpportunityFixture;
use App\DataFixtures\UserFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * 
 */
class NewOppEmailFixture extends Fixture implements OrderedFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $new = new NewOppEmail();
        $volunteer = $this->getReference(UserFixture::VOLUNTEER_REFERENCE);
        $vId = $volunteer->getId();
        $opportunity = $this->getReference(OpportunityFixture::OPPORTUNITY_REFERENCE);
        $oppId = $opportunity->getId();
        $new->setDateAdded(new \DateTime());
        $new->setVolunteerEmail([$vId=>[$oppId]]);
        $manager->persist($new);
        
        $manager->flush();
    }    

    public function getOrder()
    {
        return 5; // the order in which fixtures will be loaded
    }
}
