<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Entity\User.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="usertable")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"staff" = "Staff", "volunteer" = "Volunteer", "admin"="Admin"})
 * @UniqueEntity(fields="email", message="Email already registered")
 */
abstract class User implements UserInterface
{
    

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message="A valid email address is required")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sname;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="last_login")
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="string", nullable=true, name="confirmation_token")
     */
    private $confirmationToken;

    /**
     * @ORM\Column(nullable=true, name="token_expires_at", type="datetime")
     */
    private $tokenExpiresAt;

    protected $staff;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function setUsername($username)
    {
        $this->username = substr($this->fname, 0, 1) . $this->sname;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get sname.
     *
     * @return string
     */
    public function getSname()
    {
        return $this->sname;
    }

    public function setSname(?string $sname): self
    {
        $this->sname = $sname;

        return $this;
    }

    /**
     * Get fname.
     *
     * @return string
     */
    public function getFname()
    {
        return $this->fname;
    }

    public function setFname(?string $fname): self
    {
        $this->fname = $fname;

        return $this;
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
//        if ($role === static::ROLE_DEFAULT) {
//            return $this;
//        }
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }    
    
    public function hasRole($role) {
        return in_array(strtoupper($role), $this->roles);
    }
    
    public function hasRoleAdmin()
    {
        return (in_array('ROLE_ADMIN', $this->getRoles())) ? 'Yes' : 'No';
    }

    public function setHasRoleAdmin($isAdmin)
    {
        $roles = $this->getRoles();
        if ('Yes' === $isAdmin && 'No' === $this->hasRoleAdmin()) {
            $roles[] = 'ROLE_ADMIN';
        }
        if ('No' === $isAdmin && 'Yes' == $this->hasRoleAdmin()) {
            $key = array_search('ROLE_ADMIN', $roles);
            unset($roles[$key]);
        }
        $this->setRoles(array_values($roles));
    }

    /**
     *  @ORM\Column(type="boolean")
     */
    private $enabled;

    public function setEnabled($boolean)
    {
        $this->enabled = (bool) $boolean;

        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function getTokenExpiresAt()
    {
        return $this->tokenExpiresAt;
    }

    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function setTokenExpiresAt(\DateTime $date = null)
    {
        $this->tokenExpiresAt = $date;

        return $this;
    }

    /**
     * Used only on successful authentication
     */
    public function setLastLogin($time)
    {
        //set time to now()
        $this->lastLogin = $time;

        return $this;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }
    
    public function getFullName()
    {
        return $this->fname . ' ' . $this->sname;
    }

    // required by interface, otherwise irrelevant

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
    }
}
