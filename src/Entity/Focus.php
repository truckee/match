<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Focus
 *
 * @ORM\Table(name="focus")
 * @ORM\Entity(repositoryClass="App\Repository\FocusRepository")
 * @UniqueEntity("focus", message="Focus has already been used")
 */
class Focus
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
     * @ORM\Column(name="focus", type="string", length=45, nullable=true)
     */
    private $focus;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @ORM\ManyToMany(targetEntity="Nonprofit", mappedBy="focuses")
     */
    protected $nonprofits;

    /**
     * @ORM\ManyToMany(targetEntity="Volunteer", mappedBy="focuses")
     */
    protected $volunteers;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFocus(): ?string
    {
        return $this->focus;
    }

    public function setFocus(?string $focus): self
    {
        $this->focus = $focus;

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
