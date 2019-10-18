<?php

namespace App\Entity;

use App\Entity\Focus;
use Doctrine\ORM\Mapping as ORM;

/**
 * Organization
 *
 * @ORM\Table(name="organization")
 * @ORM\Entity
 */
class Organization
{

    public function __construct() {
        // organizations must be activated manually
        $this->temp = true;
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
     * @ORM\Column(name="orgName", type="string", length=65, nullable=true)
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
     */
    private $website;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var bool
     *
     * @ORM\Column(name="temp", type="boolean", nullable=false)
     */
    private $temp;

    /**
     * @ORM\Column(name="add_date", type="datetime")
     */
    private $addDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="areacode", type="integer", nullable=true)
     */
    private $areacode;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $ein;
    
    /**
     * @ORM\OneToOne(targetEntity="Staff", inversedBy="organization")
     * @ORM\JoinColumn(name="staff_id", referencedColumnName="id")
     */
    protected $staff;

    /**
     * @ORM\ManyToMany(targetEntity="Focus", inversedBy="organizations", cascade={"persist"})
     * @ORM\JoinTable(name="org_focus",
     *      joinColumns={@ORM\JoinColumn(name="orgId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="focusId", referencedColumnName="id")}
     *      ))
     */
    protected $focuses;

    public function getId(): ?int {
        return $this->id;
    }

    public function getOrgname(): ?string {
        return $this->orgname;
    }

    public function setOrgname(?string $orgname): self {
        $this->orgname = $orgname;

        return $this;
    }

    public function getAddress(): ?string {
        return $this->address;
    }

    public function setAddress(?string $address): self {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function setCity(?string $city): self {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string {
        return $this->state;
    }

    public function setState(?string $state): self {
        $this->state = $state;

        return $this;
    }

    public function getZip(): ?string {
        return $this->zip;
    }

    public function setZip(?string $zip): self {
        $this->zip = $zip;

        return $this;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(?string $phone): self {
        $this->phone = $phone;

        return $this;
    }

    public function getWebsite(): ?string {
        return $this->website;
    }

    public function setWebsite(?string $website): self {
        $this->website = $website;

        return $this;
    }

    public function getActive(): ?bool {
        return $this->active;
    }

    public function setActive(?bool $active): self {
        $this->active = $active;

        return $this;
    }

    public function getTemp(): ?bool {
        return $this->temp;
    }

    public function setTemp(bool $temp): self {
        $this->temp = $temp;

        return $this;
    }

    public function getAddDate(): ?\DateTimeInterface {
        return $this->addDate;
    }


    /**
     * Add focuses.
     *
     * @param \Truckee\MatchBundle\Entity\Focus $focuses
     *
     * @return Organization
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

    /**
     * Get focuses.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFocuses()
    {
        return $this->focuses;
    }

    public function getAreacode(): ?int {
        return $this->areacode;
    }

    public function setAreacode(?int $areacode): self {
        $this->areacode = $areacode;

        return $this;
    }

    public function getEin(): ?string {
        return $this->ein;
    }

    public function setEin(string $ein): self {
        $this->ein = $ein;

        return $this;
    }

}
