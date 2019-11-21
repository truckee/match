<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Entity/Staff.php

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
