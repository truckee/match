<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/LoadFocusSkillData.php

namespace App\DataFixtures\Test;

use App\Entity\Focus;
use App\Entity\Skill;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;

/**
 * Loads focus & skill data.
 */
class OptionsFixture extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{
    /**
     * Load fixtures.
     *
     * @param \Doctrine\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();
        gc_collect_cycles(); // Could be useful if you have a lot of fixtures

        $focusSeniors = new Focus();
        $focusSeniors->setFocus('Seniors');
        $focusSeniors->setEnabled(true);
        $manager->persist($focusSeniors);
        $this->setReference('focus_seniors', $focusSeniors);

        $focusEducation = new Focus();
        $focusEducation->setFocus('Education');
        $focusEducation->setEnabled(true);
        $manager->persist($focusEducation);
        $this->setReference('focus_ed', $focusEducation);

        $focusHealth = new Focus();
        $focusHealth->setFocus('Health');
        $focusHealth->setEnabled(true);
        $manager->persist($focusHealth);
        $this->setReference('focus_health', $focusHealth);

        $skillAdministrative = new Skill();
        $skillAdministrative->setSkill('Administrative Support');
        $skillAdministrative->setEnabled(true);
        $manager->persist($skillAdministrative);
        $this->setReference('skill_admin', $skillAdministrative);

        $skillBoard = new Skill();
        $skillBoard->setSkill('Board Member');
        $skillBoard->setEnabled(true);
        $manager->persist($skillBoard);
        $this->setReference('skill_board', $skillBoard);

        $skillComputers = new Skill();
        $skillComputers->setSkill('Computers & IT');
        $skillComputers->setEnabled(true);
        $manager->persist($skillComputers);
        $this->setReference('skill_computers', $skillComputers);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
