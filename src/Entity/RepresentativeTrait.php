<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/RepresentativeTrait.php

namespace App\Entity;

use App\Entity\Nonprofit;
use Doctrine\ORM\Mapping as ORM;

trait RepresentativeTrait
{

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
     * @ORM\Column(type="string", nullable=true)
     * Options: "Replace", "Pending", "Replaced", "Replacement"
     * Replace: nonprofit's representative
     * Pending: representative for which a replacement has been named
     * Replacement: previous representative
     * replacement: person named but not yet registered as representative
     */
    private $replacementStatus;

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
        if (!is_null($this->nonprofit)) {
            return $this->nonprofit->getOrgname();
        }

        return null;
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
