<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/AdminOutbox.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdminOutbox
 *
 * @ORM\Table(name="admin_outbox")
 * @ORM\Entity
 */
class AdminOutbox
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
     * @ORM\Column(name="recipient", type="integer", nullable=false)
     */
    private $recipient;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message_type", type="string", length=255, nullable=true)
     */
    private $messageType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_type", type="string", length=255, nullable=true)
     */
    private $userType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="oppId", type="integer", nullable=true)
     */
    private $oppid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="orgId", type="integer", nullable=true)
     */
    private $orgid;

    /**
     * @var string
     *
     * @ORM\Column(name="function", type="string", length=255, nullable=false)
     */
    private $function;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipient(): ?int
    {
        return $this->recipient;
    }

    public function setRecipient(int $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    public function setMessageType(?string $messageType): self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(?string $userType): self
    {
        $this->userType = $userType;

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

    public function getOrgid(): ?int
    {
        return $this->orgid;
    }

    public function setOrgid(?int $orgid): self
    {
        $this->orgid = $orgid;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

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
}
