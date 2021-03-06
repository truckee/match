<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/Nonprofit.php

namespace App\Entity;

use App\Entity\Focus;
use App\Entity\Opportunity;
use App\Entity\Person;
use App\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Nonprofit
 *
 * @ORM\Table(name="nonprofit")
 * @ORM\Entity
 * @UniqueEntity(fields="ein", message="Nonprofit is already registered")
 * @ORM\HasLifecycleCallbacks()
 */
class Nonprofit
{

    public function __construct()
    {
        $this->opportunities = new ArrayCollection();
        $this->reps = new ArrayCollection();
        $this->focuses = new ArrayCollection();
        // nonprofits must be activated manually
        $this->addDate = new \DateTime();
        $this->active = false;
    }

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
     * @ORM\Column(name="orgName", type="string", nullable=true)
     * @Assert\NotBlank(message="Nonprofit name is required")
     */
    private $orgname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=50, nullable=true)
     */
    private $address;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state", type="string", length=50, nullable=true)
     */
    private $state;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip", type="string", length=10, nullable=true)
     */
    private $zip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="website", type="string", length=50, nullable=true)
     * @CustomAssert\URLConstraint
     */
    private $website;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(name="add_date", type="datetime")
     */
    private $addDate;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="EIN is required")
     * @Assert\Length(min = 9, max = 9, exactMessage = "EIN has 9 digits")
     */
    private $ein;

    /**
     * @ORM\OneToMany(targetEntity="Opportunity", mappedBy="nonprofit", cascade={"persist","remove"}, fetch="EAGER")
     */
    protected $opportunities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Person", mappedBy="nonprofit", cascade={"persist", "remove"})
     */
    protected $reps;

    /**
     * @ORM\ManyToMany(targetEntity="Focus", inversedBy="nonprofits", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinTable(name="org_focus",
     *      joinColumns={@ORM\JoinColumn(name="orgId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="focusId", referencedColumnName="id")}
     *      ))
     */
    protected $focuses;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrgname(): ?string
    {
        return $this->orgname;
    }

    public function setOrgname(?string $orgname): self
    {
        $this->orgname = $orgname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getReps()
    {
        return $this->reps;
    }

    public function addRep($rep): self
    {
        $this->reps[] = $rep;

        return $this;
    }

    public function removeRep(Person $rep)
    {
        $this->reps->removeElement($rep);
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        if (null !== $website && !preg_match("^(http|https)://^", $website)) {
            $this->website = "http://" . $website;
        } else {
            $this->website = $website;
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getAddDate(): ?\DateTimeInterface
    {
        return $this->addDate;
    }

    /**
     * Add focuses.
     *
     * @return Nonprofit
     */
    public function addFocus(Focus $focus)
    {
        $this->focuses[] = $focus;

        return $this;
    }

    /**
     * Remove focuses.
     *
     */
    public function removeFocus(Focus $focus)
    {
        $this->focuses->removeElement($focus);
    }

    public function getOpportunities()
    {
        return $this->opportunities;
    }

    public function addOpportunity(Opportunity $opportunity)
    {
        $this->opportunities[] = $opportunity;
        $opportunity->setNonprofit($this);

        return $this;
    }

    public function removeOpportunity(Opportunity $opportunity)
    {
        $this->opportunities->removeElement($opportunity);
    }

    /**
     * Get focuses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFocuses()
    {
        return $this->focuses;
    }

    public function getEin(): ?string
    {
        return $this->ein;
    }

    public function setEin(string $ein): self
    {
        $this->ein = $ein;

        return $this;
    }

    public function getRepresentative()
    {
        $reps = $this->reps;
        foreach ($reps as $person) {
            if ($person->getReplacementStatus() === 'Replace') {
                $rep = $person;
            }
        }

        return $rep;
    }

}
