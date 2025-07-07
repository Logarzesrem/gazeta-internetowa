<?php

/**
 * User Entity.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User entity.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered.')]
#[UniqueEntity(fields: ['username'], message: 'This username is already taken.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Email is required.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    #[Assert\Length(max: 255, maxMessage: 'Email is too long.')]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Username is required.')]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Username must be at least 3 characters long.',
        maxMessage: 'Username cannot exceed 50 characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_]+$/',
        message: 'Username can only contain letters, numbers, and underscores.'
    )]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Name is required.')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Name must be at least 2 characters long.',
        maxMessage: 'Name cannot exceed 255 characters.'
    )]
    private ?string $name = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column(nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $comments;

    /**
     * Plain password field - not stored in database, used only for validation.
     */
    #[Assert\NotBlank(groups: ['registration'], message: 'Password is required.')]
    #[Assert\Length(
        min: 8,
        max: 50,
        minMessage: 'Password must be at least 8 characters long.',
        maxMessage: 'Password cannot exceed 50 characters.',
        groups: ['registration', 'password_change']
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/',
        message: 'Password must contain at least one letter and one number.',
        groups: ['registration', 'password_change']
    )]
    private ?string $plainPassword = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->roles = ['ROLE_USER'];
        $this->comments = new ArrayCollection();
    }

    /**
     * Get ID.
     *
     * @return int|null ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get email.
     *
     * @return string|null Email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email Email
     *
     * @return static Self
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string|null Username
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username Username
     *
     * @return static Self
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string|null Password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password Password
     *
     * @return static Self
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get plain password.
     *
     * @return string|null Plain password
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Set plain password.
     *
     * @param string|null $plainPassword Plain password
     *
     * @return static Self
     */
    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null Name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name Name
     *
     * @return static Self
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get roles.
     *
     * @return array Roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set roles.
     *
     * @param array $roles Roles
     *
     * @return static Self
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get created at.
     *
     * @return \DateTimeImmutable|null Created at
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Get last login at.
     *
     * @return \DateTimeImmutable|null Last login at
     */
    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    /**
     * Set last login at.
     *
     * @param \DateTimeImmutable|null $lastLoginAt Last login at
     *
     * @return static Self
     */
    public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * Check if user is active.
     *
     * @return bool True if user is active
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Set user active status.
     *
     * @param bool $isActive Active status
     *
     * @return static Self
     */
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get avatar.
     *
     * @return string|null Avatar
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Set avatar.
     *
     * @param string|null $avatar Avatar
     *
     * @return static Self
     */
    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get bio.
     *
     * @return string|null Bio
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * Set bio.
     *
     * @param string|null $bio Bio
     *
     * @return static Self
     */
    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Get user identifier for authentication.
     *
     * @return string User identifier (email)
     */
    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    /**
     * Erase sensitive data from user.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
