<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Staff
 *
 * @ORM\Table(name="staff", indexes={@ORM\Index(name="IDX_426EF3923A8AF33E", columns={"orgId"})})
 * @ORM\Entity
 */
class Staff
{
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
     * @var \Person
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private $id;

    public function getOrgid(): ?Organization
    {
        return $this->orgid;
    }

    public function setOrgid(?Organization $orgid): self
    {
        $this->orgid = $orgid;

        return $this;
    }

    public function getId(): ?Person
    {
        return $this->id;
    }

    public function setId(?Person $id): self
    {
        $this->id = $id;

        return $this;
    }


}
