<?php

/**
 * Category entity.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Category.
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'A category with this name already exists.')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'validation.name.not_blank')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'validation.name.too_short',
        maxMessage: 'validation.name.too_long'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Regex(
        pattern: '/^[a-z0-9-]+$/',
        message: 'validation.slug.invalid'
    )]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Article::class)]
    private Collection $articles;

    /**
     * Virtual property for article count - not persisted to database.
     */
    public ?int $articleCount = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
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
     * Get slug.
     *
     * @return string|null Slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set slug.
     *
     * @param string $slug Slug
     *
     * @return static Self
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get articles.
     *
     * @return Collection<int, Article> Articles
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * Add article.
     *
     * @param Article $article Article entity
     *
     * @return static Self
     */
    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setCategory($this);
        }

        return $this;
    }

    /**
     * Remove article.
     *
     * @param Article $article Article entity
     *
     * @return static Self
     */
    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // Set the owning side to null (unless already changed)
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }

        return $this;
    }
}
