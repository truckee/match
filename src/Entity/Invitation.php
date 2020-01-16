<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="invitation")
 * @ORM\Entity
 */
class Invitation
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Email address is required")
     * @Assert\Email(message="A valid email address is required")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="First name is required")
     */
    private $fname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Last name is required")
     */
    private $sname;

    /**
     * @ORM\Column(nullable=true, name="token_expires_at", type="datetime")
     */
    private $tokenExpiresAt;

    /**
     * @ORM\Column(type="string", nullable=true, name="confirmation_token")
     */
    private $confirmationToken;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get sname.
     *
     * @return string
     */
    public function getSname()
    {
        return $this->sname;
    }

    public function setSname(?string $sname): self
    {
        $this->sname = $sname;

        return $this;
    }

    /**
     * Get fname.
     *
     * @return string
     */
    public function getFname()
    {
        return $this->fname;
    }

    public function setFname(?string $fname): self
    {
        $this->fname = $fname;

        return $this;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getTokenExpiresAt()
    {
        return $this->tokenExpiresAt;
    }

    public function setTokenExpiresAt(\DateTime $date = null)
    {
        $this->tokenExpiresAt = $date;

        return $this;
    }
}
