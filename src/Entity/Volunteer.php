<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/Volunteer.php

namespace App\Entity;

use App\Entity\Focus;
use App\Entity\Skill;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="volunteer")
 */
class Volunteer extends User
{   
    public function __construct() {
        $this->addRole('ROLE_VOLUNTEER');
    }
    
    /**
     * @var int
     *
     * @ORM\Column(name="receive_email", type="boolean", nullable=true)
     */
    protected $receiveEmail;

    /**
     * Set receiveEmail.
     *
     * @param string $receiveEmail
     *
     * @return Person
     */
    public function setReceiveEmail($receiveEmail)
    {
        $this->receiveEmail = $receiveEmail;

        return $this;
    }

    /**
     * Get receiveEmail.
     *
     * @return string
     */
    public function getReceiveEmail()
    {
        return $this->receiveEmail;
    }
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Focus", inversedBy="volunteers", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="vol_focus",
     *      joinColumns={@ORM\JoinColumn(name="volId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="focusId", referencedColumnName="id")}
     *      ))
     */
    protected $focuses;

    /**
     * Add focuses.
     *
     * @param Focus $focuses
     *
     * @return Opportunity
     */
    public function addFocus(Focus $focus)
    {
        $this->focuses[] = $focus;

        return $this;
    }

    /**
     * Remove focuses.
     *
     * @param Focus $focuses
     */
    public function removeFocus(Focus $focus)
    {
        $this->focuses->removeElement($focus);
    }

    /**
     * Get focuses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFocuses()
    {
        return $this->focuses;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Skill", inversedBy="volunteers", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="vol_skill",
     *      joinColumns={@ORM\JoinColumn(name="volId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skillId", referencedColumnName="id")}
     *      ))
     * @Assert\NotNull(message="Please select at least one", groups={"skill_required"})
     */
    protected $skills;

    /**
     * Add skills.
     *
     * @param Skill $skills
     *
     * @return Opportunity
     */
    public function addSkill(Skill $skill)
    {
        $this->skills[] = $skill;

        return $this;
    }

    /**
     * Remove skills.
     *
     * @param Skill $skills
     */
    public function removeSkill(Skill $skill)
    {
        $this->skills->removeElement($skill);
    }

    /**
     * Get skills.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkills()
    {
        return $this->skills;
    }
    
}
