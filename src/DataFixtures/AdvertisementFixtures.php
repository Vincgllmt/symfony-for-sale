<?php

namespace App\DataFixtures;

use App\Factory\AdvertisementFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdvertisementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        AdvertisementFactory::createMany(500);
    }
}
