<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/UserManager.php

namespace App\Services;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * 
 */
class UserManager
{
    private $em;
    private $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }
    
    public function createAdmin($user)
    {
        $admin = new Admin();
        $admin->setFname($user['fname']);
        $admin->setSname($user['sname']);
        $admin->setEmail($user['email']);
        $admin->setEnabled(true);
        $admin->setPassword($this->encoder->encodePassword(
                            $admin,
                            $user['password']));
        $admin->setRoles([
            'ROLE_VOLUNTEER',
            'ROLE_STAFF',
            'ROLE_ADMIN',
        ]);
        $this->em->persist($admin);
        $this->em->flush();
        
        return $admin;
    }

}
