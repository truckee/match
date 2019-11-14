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
class LoadFocusSkillData extends Fixture implements OrderedFixtureInterface
{

    /**
     * Load fixtures.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manager->clear();
        gc_collect_cycles(); // Could be useful if you have a lot of fixtures

        $focus = new Focus();
        $focus->setFocus('Animal Welfare');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Seniors');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Arts and Culture');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Civic and Public Benefit');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Education');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Environment and Conservation');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Health');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Human Services');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Recreation');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $focus = new Focus();
        $focus->setFocus('Youth Development');
        $focus->setEnabled(true);
        $manager->persist($focus);

        $skill = new Skill();
        $skill->setSkill('Administrative Support');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Board Member');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Computers & IT');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Construction/Handy Man');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Driving');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Fundraising/Grant Writing');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Health Care');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Management');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Marketing/PR');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Customer Service');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Mentoring/Tutoring');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Web/Graphics Design');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Legal Services');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $skill = new Skill();
        $skill->setSkill('Accounting/Bookkeeping');
        $skill->setEnabled(true);
        $manager->persist($skill);

        $this->addReference('focus', $focus);
        $this->addReference('skill', $skill);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
