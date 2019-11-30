<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Security/UserChecker.php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 
 */
class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->hasRole('role_staff') && !$user->hasRole('ROLE_ADMIN') && !$user->getNonprofit()->isActive()) {
            throw new CustomUserMessageAuthenticationException('Nonprofit has not yet been activated');
        }
        if (!$user->isEnabled()) {
            throw new CustomUserMessageAuthenticationException('Account has not been confirmed');
        }
    }
}
