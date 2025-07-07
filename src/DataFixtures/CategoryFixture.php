<?php

/**
 * Fixture for loading categories.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class CategoryFixture.
 */
class CategoryFixture extends Fixture
{
    /**
     * Load fixtures.
     *
     * @param ObjectManager $manager Object manager
     */
    public function load(ObjectManager $manager): void
    {
        // Clear existing categories
        $manager->getRepository(Category::class)->createQueryBuilder('c')->delete()->getQuery()->execute();

        $category1 = new Category();
        $category1->setName('Technologia');
        $category1->setSlug('technologia');
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('Kultura');
        $category2->setSlug('kultura');
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName('Nowiny');
        $category3->setSlug('nowiny');
        $manager->persist($category3);

        $manager->flush();
    }
}
