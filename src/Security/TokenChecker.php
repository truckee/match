<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/insert_path_here/TokenChecker.php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 *
 */
class TokenChecker
{
    private $em;
    private $session;
    private $token;
    
    public function __construct(EntityManagerInterface $em, SessionInterface $session, $token = null)
    {
        $this->em = $em;
        $this->session = $session;
        $this->token = $token;
    }
    
    public function checkToken($token)
    {
        if (null === $token) {
            $this->session->getFlashBag()->add('danger', 'Registration status cannot be determined');
            
            return null;
        }
        
        $user = $this->em->getRepository('App:User')->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            $this->session->getFlashBag()->add('danger', 'Invalid registration data');
            
            return null;
        }

        return $user;
    }
}
