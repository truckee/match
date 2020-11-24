<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src\Entity\User.php

namespace App\Entity;

use App\Entity\AdminTrait;
use App\Entity\RepresentativeTrait;
use App\Entity\VolunteerTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="person")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 */
class Person implements UserInterface
{

    use AdminTrait;
    use RepresentativeTrait;
    use VolunteerTrait;

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

    /**
     * @ORM\Column(type="boolean")
     */
    private $locked = false;

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

    public function setUsername()
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
        $ucRole = strtoupper($role);
        if (!in_array($ucRole, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function hasRole($role)
    {
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

    public function getEnabled()
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

    public function isLocked()
    {
        return $this->locked;
    }

    public function setLocked($lock)
    {
        $this->locked = $lock;

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

    // volunteer properties $ methods
    // required by interface, otherwise irrelevant

    public function eraseCredentials()
    {

    }

    public function getSalt()
    {

    }

}
