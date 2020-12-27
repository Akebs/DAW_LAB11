<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Customers
 *
 * @ORM\Table(name="customers", uniqueConstraints={@ORM\UniqueConstraint(name="index_users_on_email", columns={"email"})})
 * @ORM\Entity(repositoryClass="App\Repository\CustomersRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Customers implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $name = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt ;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt ;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password_digest", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $passwordDigest = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="remember_digest", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $rememberDigest = 'NULL';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="admin", type="boolean", nullable=true, options={"default"="NULL"})
     */
    private $admin = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="activation_digest", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $activationDigest = 'NULL';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="activated", type="boolean", nullable=true, options={"default"="NULL"})
     */
    private $activated = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="activated_at", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $activatedAt ;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reset_digest", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $resetDigest = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="reset_sent_at", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $resetSentAt ;




    // Implemented UserInterface methodss


    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles(): array
    {        
        return ['ROLE_USER'];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword(){
        return $this->passwordDigest;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()    
    {        
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
        public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {        
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPasswordDigest(): ?string
    {
        return $this->passwordDigest;
    }

    public function setPasswordDigest(?string $passwordDigest): self
    {
        $this->passwordDigest = $passwordDigest;

        return $this;
    }

    public function getRememberDigest(): ?string
    {
        return $this->rememberDigest;
    }

    public function setRememberDigest(?string $rememberDigest): self
    {
        $this->rememberDigest = $rememberDigest;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(?bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getActivationDigest(): ?string
    {
        return $this->activationDigest;
    }

    public function setActivationDigest(?string $activationDigest): self
    {
        $this->activationDigest = $activationDigest;

        return $this;
    }

    public function getActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(?bool $activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    public function getActivatedAt(): ?\DateTimeInterface
    {
        return $this->activatedAt;
    }

    public function setActivatedAt(?\DateTimeInterface $activatedAt): self
    {
        $this->activatedAt = $activatedAt;

        return $this;
    }

    public function getResetDigest(): ?string
    {
        return $this->resetDigest;
    }

    public function setResetDigest(?string $resetDigest): self
    {
        $this->resetDigest = $resetDigest;

        return $this;
    }

    public function getResetSentAt(): ?\DateTimeInterface
    {
        return $this->resetSentAt;
    }

    public function setResetSentAt(?\DateTimeInterface $resetSentAt): self
    {
        $this->resetSentAt = $resetSentAt;

        return $this;
    }




}
