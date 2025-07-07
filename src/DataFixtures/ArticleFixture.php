<?php

/**
 * Fixture for loading articles.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class ArticleFixture.
 */
class ArticleFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * Load fixtures.
     *
     * @param ObjectManager $manager Object manager
     */
    public function load(ObjectManager $manager): void
    {
        // Clear existing articles
        $manager->getRepository(Article::class)->createQueryBuilder('a')->delete()->getQuery()->execute();

        // Get admin user and categories
        $adminUser = $manager->getRepository(AdminUser::class)->findOneBy([]);
        $category1 = $manager->getRepository(Category::class)->findOneBy(['slug' => 'technologia']);
        $category2 = $manager->getRepository(Category::class)->findOneBy(['slug' => 'kultura']);
        $category3 = $manager->getRepository(Category::class)->findOneBy(['slug' => 'nowiny']);

        if (!$adminUser) {
            throw new \RuntimeException('AdminUser not found. Make sure AdminUserFixture is loaded first.');
        }

        $article1 = new Article();
        $article1->setTitle('Renesans winyli: Powrót czarnej płyty do łask');
        $article1->setContent('Artykuł o powrocie popularności płyt winylowych wśród melomanów i kolekcjonerów.');
        $article1->setAuthor($adminUser);
        $article1->setCategory($category2); // Culture
        $manager->persist($article1);

        $article2 = new Article();
        $article2->setTitle(
            'Sztuczna inteligencja w codziennym życiu: Jak AI zmienia nasze nawyki?'
        );
        $article2->setContent(
            'Analiza wpływu sztucznej inteligencji na nasze codzienne życie i zmieniające się nawyki.'
        );
        $article2->setAuthor($adminUser);
        $article2->setCategory($category1); // Technology
        $manager->persist($article2);

        $article3 = new Article();
        $article3->setTitle('Zamknięcie mostu na trzy tygodnie. Objazdy już wyznaczone');
        $article3->setContent('Informacje o planowanym zamknięciu mostu i wyznaczonych objazdach dla kierowców.');
        $article3->setAuthor($adminUser);
        $article3->setCategory($category3); // News
        $manager->persist($article3);

        $article4 = new Article();
        $article4->setTitle('Teatr w wirtualnej rzeczywistości: Sztuka w epoce VR');
        $article4->setContent('Eksploracja nowych możliwości teatru dzięki technologiom wirtualnej rzeczywistości.');
        $article4->setAuthor($adminUser);
        $article4->setCategory($category2); // Culture
        $manager->persist($article4);

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
        ];
    }
}
