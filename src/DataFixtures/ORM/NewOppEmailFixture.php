<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/NewOppEmailFixture.php

namespace App\DataFixtures\ORM;

use App\Entity\NewOppEmail;
use App\DataFixtures\ORM\OpportunityFixture;
use App\DataFixtures\ORM\UserFixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * 
 */
class NewOppEmailFixture extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $new = new NewOppEmail();
        $volunteer = $this->fixtures->getReference('volunteer');
        $vId = $volunteer->getId();
        $opportunity = $this->fixtures->getReference('opportunity');
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
