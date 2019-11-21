<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/Search.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Search
 *
 * @ORM\Table(name="search")
 * @ORM\Entity
 */
class Search
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \Opportunity
     *
     * @ORM\ManyToOne(targetEntity="Opportunity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opp_id", referencedColumnName="id")
     * })
     */
    private $opp;

    /**
     * @var \Focus
     *
     * @ORM\ManyToOne(targetEntity="Focus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="focus_id", referencedColumnName="id")
     * })
     */
    private $focus;

    /**
     * @var \Skill
     *
     * @ORM\ManyToOne(targetEntity="Skill")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="skill_id", referencedColumnName="id")
     * })
     */
    private $skill;

    /**
     * @var \Nonprofit
     *
     * @ORM\ManyToOne(targetEntity="Nonprofit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     * })
     */
    private $org;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getOpp(): ?Opportunity
    {
        return $this->opp;
    }

    public function setOpp(?Opportunity $opp): self
    {
        $this->opp = $opp;

        return $this;
    }

    public function getFocus(): ?Focus
    {
        return $this->focus;
    }

    public function setFocus(?Focus $focus): self
    {
        $this->focus = $focus;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    public function getOrg(): ?Nonprofit
    {
        return $this->org;
    }

    public function setOrg(?Nonprofit $org): self
    {
        $this->org = $org;

        return $this;
    }
}
