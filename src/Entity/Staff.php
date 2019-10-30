<?php

namespace App\Entity;

use App\Entity\User as User;
use App\Entity\Nonprofit;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Staff
 *
 * @ORM\Table(name="staff")
 * @ORM\Entity
 */
class Staff extends User
{
    public function __construct()
    {
        $this->addRole('ROLE_STAFF');
    }
    
    /**
     * @ORM\OneToOne(targetEntity="Nonprofit", mappedBy="staff")
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
}
