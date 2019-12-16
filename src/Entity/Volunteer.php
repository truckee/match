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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass = "App\Repository\VolunteerRepository")
 * @ORM\Table(name="volunteer")
 * @ORM\HasLifecycleCallbacks()
 */
class Volunteer extends User
{
    public function __construct()
    {
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
     * @param Focus $focus
     *
     * @return Opportunity
     */
    public function addFocus(Focus $focus)
    {
        $this->focuses[] = $focus;
        array_push($this->jsonFocus, $focus->getId());
 
        return $this;
    }

    /**
     * Remove focuses.
     *
     * @param Focus $focus
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
     * @ORM\Column(type="json_array")
     */
    private $jsonFocus = [];

    /**
     * @ORM\Column(type="json_array")
     */
    private $jsonSkill = [];

    /**
     * Add skills.
     *
     * @param Skill $skill
     *
     * @return Opportunity
     */
    public function addSkill(Skill $skill)
    {
        $this->skills[] = $skill;
        array_push($this->jsonSkill, $skill->getId());

        return $this;
    }

    /**
     * Remove skills.
     *
     * @param Skill $skill
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

    public function getJsonFocus(): ?array
    {
        return $this->jsonFocus;
    }

    public function getJsonSkill(): ?array
    {
        return $this->jsonSkill;
    }
    
    /**
     * @ORM\PreUpdate()
     */
    public function updateJsonFocus()
    {
        $focuses = $this->getFocuses();
        $this->jsonFocus = [];
        foreach ($focuses as $item) {
            array_push($this->jsonFocus, $item->getId());
        }
    }
    
    /**
     * @ORM\PreUpdate()
     */
    public function updateJsonSkills()
    {
        $skills = $this->skills;
        $this->jsonSkill = [];
        foreach ($skills as $item) {
            array_push($this->jsonSkill, $item->getId());
        }
    }
}
