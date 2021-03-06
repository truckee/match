<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/NewOppEmailFixture.php

namespace App\DataFixtures\Test;

use App\Entity\NewOppEmail;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;

/**
 *
 */
class NewOppEmailFixture extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $new = new NewOppEmail();
        $volunteer = $this->getReference('volunteer');
        $vId = $volunteer->getId();
        $opportunity = $this->getReference('opp');
        $oppId = $opportunity->getId();
        $new->setDateAdded(new \DateTime());
        $new->setNVolunteers(1);
        $manager->persist($new);
        
        $manager->flush();
    }

    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
