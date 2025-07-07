<?php

/**
 * AdminUser Entity.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AdminUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AdminUser entity represents an administrator in the system.
 *
 * This entity implements UserInterface and PasswordAuthenticatedUserInterface
 * to integrate with Symfony's security system.
 */
#[ORM\Entity(repositoryClass: AdminUserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered.')]
class AdminUser implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(length: 255)]
    private ?string $password = null;

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
        pattern: '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
        message: 'Password must contain at least one letter and one number.',
        groups: ['registration', 'password_change']
    )]
    private ?string $plainPassword = null;

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

    /**
     * Get the admin user's ID.
     *
     * @return int|null The admin user's ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the admin user's email address.
     *
     * @return string|null The admin user's email address
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the admin user's email address.
     *
     * @param string $email The email address
     *
     * @return static Self
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the hashed password.
     *
     * @return string|null The hashed password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the hashed password.
     *
     * @param string $password The hashed password
     *
     * @return static Self
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the plain password (not stored in database).
     *
     * @return string|null The plain password
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Set the plain password.
     *
     * @param string|null $plainPassword The plain password
     *
     * @return static Self
     */
    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get the admin user's name.
     *
     * @return string|null The admin user's name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the admin user's name.
     *
     * @param string $name The name
     *
     * @return static Self
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the user roles.
     *
     * @return string[] The user roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_ADMIN';
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set the user roles.
     *
     * @param string[] $roles The user roles
     *
     * @return static Self
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the user identifier (email in this case).
     *
     * @return string The user identifier (email)
     */
    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    /**
     * This method is required by the UserInterface but we don't need it
     * since we're not storing any sensitive data temporarily.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
