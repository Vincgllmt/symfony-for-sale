<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = explode(PHP_EOL, file_get_contents(__DIR__.'/../../data/category.txt'));
        foreach ($categories as $category) {
            CategoryFactory::createOne(['name' => $category]);
        }
    }
}
