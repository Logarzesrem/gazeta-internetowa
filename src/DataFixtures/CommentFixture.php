<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Clear existing comments
        $manager->createQuery('DELETE FROM App\Entity\Comment')->execute();

        // Get articles and users
        $articles = $manager->getRepository(Article::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        if (empty($articles) || empty($users)) {
            throw new \RuntimeException('Articles or Users not found. Make sure ArticleFixture and UserFixture are loaded first.');
        }

        $comment1 = new Comment();
        $comment1->setContent('Bardzo ciekawy artykuł o winylach!');
        $comment1->setArticle($articles[0]); // First article
        $comment1->setAuthor($users[0]); // First user
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setContent('AI rzeczywiście zmienia wszystko wokół nas.');
        $comment2->setArticle($articles[1]); // Second article
        $comment2->setAuthor($users[1]); // Second user
        $manager->persist($comment2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ArticleFixture::class,
            UserFixture::class,
        ];
    }
}
