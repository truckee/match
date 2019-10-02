<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FocusMap
 *
 * @ORM\Table(name="focus_map", indexes={@ORM\Index(name="idx_key", columns={"focusId", "orgId", "volId"})})
 * @ORM\Entity
 */
class FocusMap
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
     * @ORM\Column(name="focusId", type="integer", nullable=false)
     */
    private $focusid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="orgId", type="integer", nullable=true)
     */
    private $orgid;

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

    public function getFocusid(): ?int
    {
        return $this->focusid;
    }

    public function setFocusid(int $focusid): self
    {
        $this->focusid = $focusid;

        return $this;
    }

    public function getOrgid(): ?int
    {
        return $this->orgid;
    }

    public function setOrgid(?int $orgid): self
    {
        $this->orgid = $orgid;

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
