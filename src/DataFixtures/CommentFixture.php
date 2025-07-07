<?php

/**
 * Fixture for loading comments.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CommentFixture.
 */
class CommentFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * Load fixtures.
     *
     * @param ObjectManager $manager Object manager
     */
    public function load(ObjectManager $manager): void
    {
        // Clear existing comments
        $manager->getRepository(Comment::class)->createQueryBuilder('c')->delete()->getQuery()->execute();

        // Get articles and users
        $articles = $manager->getRepository(Article::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($articles) || empty($users)) {
            throw new \RuntimeException('Articles or Users not found. Make sure ArticleFixture and UserFixture are loaded first.');
        }

        // Ensure we have at least 2 users and 2 articles
        if (count($users) < 2) {
            throw new \RuntimeException('At least 2 users are required for CommentFixture.');
        }

        if (count($articles) < 2) {
            throw new \RuntimeException('At least 2 articles are required for CommentFixture.');
        }

        $comment1 = new Comment();
        $comment1->setContent('Świetny artykuł! Bardzo pouczające informacje o technologii.');
        $comment1->setArticle($articles[0]);
        $comment1->setAuthor($users[0]);
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setContent('Dziękuję za ten artykuł. Pomógł mi zrozumieć wiele kwestii.');
        $comment2->setArticle($articles[0]);
        $comment2->setAuthor($users[1]);
        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3->setContent('Interesujący punkt widzenia. Czekam na więcej takich artykułów.');
        $comment3->setArticle($articles[1]);
        $comment3->setAuthor($users[0]);
        $manager->persist($comment3);

        $comment4 = new Comment();
        $comment4->setContent('Bardzo dobrze napisane. Polecam wszystkim zainteresowanym tematem.');
        $comment4->setArticle($articles[1]);
        $comment4->setAuthor($users[1]);
        $manager->persist($comment4);

        $manager->flush();
    }

    /**
     * Get dependencies.
     *
     * @return array Dependencies
     */
    public function getDependencies(): array
    {
        return [
            AdminUserFixture::class,
            CategoryFixture::class,
            ArticleFixture::class,
        ];
    }
}
