<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/DataFixtures/LoadFocusSkillData.php

namespace App\DataFixtures;

use App\Entity\Focus;
use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Loads focus & skill data.
 */
class OptionsFixture extends Fixture implements OrderedFixtureInterface
{
    public const SENIORS_FOCUS_REFERENCE = 'seniors-focus';
    public const EDUCATION_FOCUS_REFERENCE = 'education-focus';
    public const HEALTH_FOCUS_REFERENCE = 'health-focus';
    public const ADMIN_SKILL_REFERENCE = 'admin-skill';
    public const BOARD_SKILL_REFERENCE = 'board-skill';
    public const COMPUTERS_SKILL_REFERENCE = 'computers-skill';
    
    /**
     * Load fixtures.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();
        gc_collect_cycles(); // Could be useful if you have a lot of fixtures

        $focusSeniors = new Focus();
        $focusSeniors->setFocus('Seniors');
        $focusSeniors->setEnabled(true);
        $manager->persist($focusSeniors);
        $this->addReference(self::SENIORS_FOCUS_REFERENCE, $focusSeniors);

        $focusEducation = new Focus();
        $focusEducation->setFocus('Education');
        $focusEducation->setEnabled(true);
        $manager->persist($focusEducation);
        $this->addReference(self::EDUCATION_FOCUS_REFERENCE, $focusEducation);

        $focusHealth = new Focus();
        $focusHealth->setFocus('Health');
        $focusHealth->setEnabled(true);
        $manager->persist($focusHealth);
        $this->addReference(self::HEALTH_FOCUS_REFERENCE, $focusHealth);

        $skillAdministrative = new Skill();
        $skillAdministrative->setSkill('Administrative Support');
        $skillAdministrative->setEnabled(true);
        $manager->persist($skillAdministrative);
        $this->addReference(self::ADMIN_SKILL_REFERENCE, $skillAdministrative);

        $skillBoard = new Skill();
        $skillBoard->setSkill('Board Member');
        $skillBoard->setEnabled(true);
        $manager->persist($skillBoard);
        $this->addReference(self::BOARD_SKILL_REFERENCE, $skillBoard);

        $skillComputers = new Skill();
        $skillComputers->setSkill('Computers & IT');
        $skillComputers->setEnabled(true);
        $manager->persist($skillComputers);
        $this->addReference(self::COMPUTERS_SKILL_REFERENCE, $skillComputers);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
