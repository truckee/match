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
 */
class Admin extends User
{
    public function __construct()
    {
        // allow admin to modify volunteers & staff & nonprofits
        $this->roles = [
            'ROLE_ADMIN',
        ];
    }
    
    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $activator;
    
    private $admin_enabled;
    
    public function isActivator()
    {
        return $this->activator;
    }
    
    public function setActivator($activator)
    {
        $this->activator = $activator;
        
        return $this;
    }
    
    public function getAdminEnabled()
    {
        if (!$this->hasRole('ROLE_SUPER_ADMIN')) {
            return $this->isEnabled();
        } else {
            return null;
        }
    }
}
