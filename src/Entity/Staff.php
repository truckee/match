<?php

namespace App\Entity;

use App\Entity\User as User;
use App\Entity\Organization;
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
     * @ORM\OneToOne(targetEntity="Organization", mappedBy="staff")
     */
    protected $organization;

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }
}
