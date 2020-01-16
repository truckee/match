<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/Admin.php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="admin")
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already registered")
 */
class Admin extends User
{
    public function __construct()
    {
        // allow admin to modify volunteers & staff & nonprofits
        $this->roles = [
            'ROLE_VOLUNTEER',
            'ROLE_STAFF',
            'ROLE_ADMIN',
        ];
    }
    
    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $activater;
    
    public function isActivater()
    {
        return $this->activater;
    }
    
    public function setActivater($activater)
    {
        $this->activater = $activater;
        
        return $this;
    }
}
