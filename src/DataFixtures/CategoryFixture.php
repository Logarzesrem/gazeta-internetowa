<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Clear existing categories
        $manager->createQuery('DELETE FROM App\Entity\Category')->execute();

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
