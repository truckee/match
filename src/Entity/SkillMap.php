<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SkillMap
 *
 * @ORM\Table(name="skill_map", indexes={@ORM\Index(name="idx_key", columns={"skillId", "oppId", "volId"})})
 * @ORM\Entity
 */
class SkillMap
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
     * @var int
     *
     * @ORM\Column(name="skillId", type="integer", nullable=false)
     */
    private $skillid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="oppId", type="integer", nullable=true)
     */
    private $oppid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="volId", type="integer", nullable=true)
     */
    private $volid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkillid(): ?int
    {
        return $this->skillid;
    }

    public function setSkillid(int $skillid): self
    {
        $this->skillid = $skillid;

        return $this;
    }

    public function getOppid(): ?int
    {
        return $this->oppid;
    }

    public function setOppid(?int $oppid): self
    {
        $this->oppid = $oppid;

        return $this;
    }

    public function getVolid(): ?int
    {
        return $this->volid;
    }

    public function setVolid(?int $volid): self
    {
        $this->volid = $volid;

        return $this;
    }


}
