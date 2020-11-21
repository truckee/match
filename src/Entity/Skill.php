<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/Skill.php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Skill
 *
 * @ORM\Table(name="skill")
 * @ORM\Entity
 * @UniqueEntity("skill", message="Skill has already been used")
 */
class Skill
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
     * @ORM\Column(name="skill", type="string", length=45, nullable=true)
     */
    private $skill;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Opportunity", mappedBy="skills")
     */
    protected $opportunities;

    /**
     * @ORM\ManyToMany(targetEntity="Volunteer", mappedBy="skills")
     */
    protected $volunteers;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkill(): ?string
    {
        return $this->skill;
    }

    public function setSkill(?string $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
