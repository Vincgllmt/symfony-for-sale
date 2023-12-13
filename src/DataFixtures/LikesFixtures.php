<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\AdvertisementFactory;
use App\Story\UserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LikesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach (UserStory::getPool('users') as $user) {
            /* @var User $user */
            $notCreatedByUser = [];
            foreach (AdvertisementFactory::randomRange(50, 100) as $adv) {
                if ($adv->object()->getOwner() !== $user) {
                    $notCreatedByUser[] = $adv;
                }
            }

            foreach ($notCreatedByUser as $adv) {
                $user->addLike($adv->object());
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AppFixtures::class,
            AdvertisementFixtures::class,
        ];
    }
}
