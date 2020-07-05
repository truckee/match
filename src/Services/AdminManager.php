<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/AdminManager.php

namespace App\Services;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 *
 */
class AdminManager
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
            $user['password']
        ));
        $admin->setActivator(true);
        $admin->setRoles([
            'ROLE_SUPER_ADMIN',
        ]);
        $this->em->persist($admin);
        $this->em->flush();
        
        return $admin;
    }
}
