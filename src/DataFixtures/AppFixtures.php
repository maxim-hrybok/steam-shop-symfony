<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         // 1. create categories as objects and persist them to the database
        $categoryGames = new Category();
        $categoryGames->setName('Steam Games');
        $manager->persist($categoryGames); // prepare to save to the database

        $categoryCards = new Category();
        $categoryCards->setName('Gift Cards');
        $manager->persist($categoryCards);

        $categoryInGame = new Category();
        $categoryInGame->setName('In-Game Items');
        $manager->persist($categoryInGame);

        // 2. Create products and associate them with categories
        for ($i = 1; $i <= 30; $i++) {
            $product = new Product();
            $product->setName('Product #'.$i);
            $product->setPrice(9.99 + $i); // Different price for each
            $product->setDescription('This is an awesome product description for item '.$i);
            $product->setStock(10 * $i);
            $product->setStatus('available');

            // 3. Associate products with categories
            if ($i % 2 === 0) {
                $product->addCategory($categoryGames);
            } elseif ($i % 3 === 0) {
                $product->addCategory($categoryCards);
            } else {
                $product->addCategory($categoryInGame);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}
