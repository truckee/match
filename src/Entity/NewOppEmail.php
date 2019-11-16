<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/NewOppEmail.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewOppEmailRepository")
 */
class NewOppEmail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json_array")
     */
    private $volunteerEmail = [];

    /**
     * @ORM\Column(type="date")
     */
    private $dateAdded;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nVolunteers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nOpportunities;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVolunteerEmail(): ?array
    {
        return $this->volunteerEmail;
    }

    public function setVolunteerEmail(array $volunteerEmail): self
    {
        $this->volunteerEmail = $volunteerEmail;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): self
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    public function getSent(): ?bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): self
    {
        $this->sent = $sent;

        return $this;
    }

    public function getNVolunteers(): ?int
    {
        return $this->nVolunteers;
    }

    public function setNVolunteers(?int $nVolunteers): self
    {
        $this->nVolunteers = $nVolunteers;

        return $this;
    }

    public function getNOpportunities(): ?int
    {
        return $this->nOpportunities;
    }

    public function setNOpportunities(?int $nOpportunities): self
    {
        $this->nOpportunities = $nOpportunities;

        return $this;
    }
}
