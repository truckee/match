<?php

namespace App\DataFixtures;

use App\Entity\Focus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FocusFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $focus = new Focus();
        $focus->setFocus('Animals');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $manager->flush();
    }
}
