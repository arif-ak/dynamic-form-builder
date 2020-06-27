<?php

namespace App\Entity;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\DateTimeCrudFields;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    use DateTimeCrudFields;

    const PASSWORD_LENGTH = 8;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $passwordExpired;

    /**
     * @var array
     */
    private $roles = [];

    private $rawPassword;

    public function generatePassword()
    {
        $password = substr(md5(uniqid(mt_rand(), true)), 0, self::PASSWORD_LENGTH);
        $password = 'password'; //temporary password for development
        $this->rawPassword = $password;

        return $password;
    }

    public function encodePassword(PasswordEncoderInterface $encoder)
    {
        if ($this->rawPassword) {
            $this->salt = sha1(uniqid(mt_rand()));
            $this->password = $encoder->encodePassword($this->rawPassword, $this->salt);

            $this->eraseCredentials();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->rawPassword = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getPasswordExpired(): ?bool
    {
        return $this->passwordExpired;
    }

    public function setPasswordExpired(bool $passwordExpired): self
    {
        $this->passwordExpired = $passwordExpired;

        return $this;
    }

    public function getRoles(): ?array
    {
        // $roles = array_unique(array_merge($this->roles, [self::ROLE_DEFAULT]));
        return ['ROLE_USER'];
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
