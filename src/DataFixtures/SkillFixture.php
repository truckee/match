<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SkillFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $skill = new Skill();
        $skill->setEnabled(true);
        $skill->setSkill('Bouncing');
        $manager->persist($skill);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
