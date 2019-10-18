<?php

namespace App\Entity;

use App\Entity\Skill;
use Doctrine\ORM\Mapping as ORM;

/**
 * Opportunity
 *
 * @ORM\Table(name="opportunity", indexes={@ORM\Index(name="IDX_8389C3D73A8AF33E", columns={"orgId"})})
 * @ORM\Entity
 */
class Opportunity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="oppName", type="string", length=66, nullable=true)
     */
    private $oppname;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="add_date", type="date", nullable=true)
     */
    private $addDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=true)
     */
    private $lastupdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="minAge", type="text", length=0, nullable=true)
     */
    private $minage;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="group_ok", type="boolean", nullable=true)
     */
    private $groupOk;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="expireDate", type="date", nullable=true)
     */
    private $expiredate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=0, nullable=true)
     */
    private $description;

    /**
     * @var \Organization
     *
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orgId", referencedColumnName="id")
     * })
     */
    private $orgid;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="background", type="boolean", nullable=true)
     */
    private $background;

    /**
     * @ORM\ManyToMany(targetEntity="Skill", inversedBy="opportunities", cascade={"persist"})
     * @ORM\JoinTable(name="opp_skill",
     *      joinColumns={@ORM\JoinColumn(name="oppId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skillId", referencedColumnName="id")}
     *      ))
     */
    protected $skills;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOppname(): ?string
    {
        return $this->oppname;
    }

    public function setOppname(?string $oppname): self
    {
        $this->oppname = $oppname;

        return $this;
    }

    public function getAddDate(): ?\DateTimeInterface
    {
        return $this->addDate;
    }

    public function setAddDate(?\DateTimeInterface $addDate): self
    {
        $this->addDate = $addDate;

        return $this;
    }

    public function getLastupdate(): ?\DateTimeInterface
    {
        return $this->lastupdate;
    }

    public function setLastupdate(?\DateTimeInterface $lastupdate): self
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    public function getMinage(): ?string
    {
        return $this->minage;
    }

    public function setMinage(?string $minage): self
    {
        $this->minage = $minage;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getGroupOk(): ?bool
    {
        return $this->groupOk;
    }

    public function setGroupOk(?bool $groupOk): self
    {
        $this->groupOk = $groupOk;

        return $this;
    }

    public function getExpiredate(): ?\DateTimeInterface
    {
        return $this->expiredate;
    }

    public function setExpiredate(?\DateTimeInterface $expiredate): self
    {
        $this->expiredate = $expiredate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOrgid(): ?Organization
    {
        return $this->orgid;
    }

    public function setOrgid(?Organization $orgid): self
    {
        $this->orgid = $orgid;

        return $this;
    }

    public function getBackground(): ?bool
    {
        return $this->background;
    }

    public function setBackground(?bool $background): self
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Add skills.
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
