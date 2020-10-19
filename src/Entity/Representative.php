<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/Representative.php

namespace App\Entity;

use App\Entity\User as User;
use App\Entity\Nonprofit;
use Doctrine\ORM\Mapping as ORM;

/**
 * Staff
 *
 * @ORM\Table(name="staff")
 * @ORM\Entity
 */
class Representative extends User
{
    public function __construct()
    {
        $this->addRole('ROLE_REP');
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Nonprofit", inversedBy="reps", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orgId", referencedColumnName="id")
     * })
     */
    protected $nonprofit;

    public function getNonprofit()
    {
        return $this->nonprofit;
    }

    public function setNonprofit(Nonprofit $nonprofit = null)
    {
        $this->nonprofit = $nonprofit;

        return $this;
    }
    
    /**
     * @ORM\Column(type="string")
     * Options: "Replace", "Pending", "Replaced", "Replacement"
     * Replace: nonprofit's representative
     * Pending: representative for which a replacement has been named
     * Replacement: previous representative
     * replacement: person named but not yet registered as representative
     */
    public $replacementStatus;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $initiated;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $completed;
    
    public function getReplacementStatus()
    {
        return $this->replacementStatus;
    }
    
    public function setReplacementStatus($replacementStatus)
    {
        $this->replacementStatus = $replacementStatus;
        
        return $this;
    }
    
    public function getOrgname()
    {
        return $this->nonprofit->getOrgname();
    }
    
    public function __toString()
    {
        return $this->getFname() . ' ' . $this->getSname();
    }

    public function getInitiated(): ?\DateTimeInterface
    {
        return $this->initiated;
    }

    public function setInitiated(?\DateTimeInterface $initiated): self
    {
        $this->initiated = $initiated;

        return $this;
    }

    public function getCompleted(): ?\DateTimeInterface
    {
        return $this->completed;
    }

    public function setCompleted(?\DateTimeInterface $completed): self
    {
        $this->completed = $completed;

        return $this;
    }
}
