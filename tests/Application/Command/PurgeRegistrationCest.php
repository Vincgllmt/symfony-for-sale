<?php

namespace App\Tests\Application\Command;

use App\Factory\UserFactory;
use App\Tests\Support\ApplicationTester;

class PurgeRegistrationCest
{
    public function purgeRegistration(ApplicationTester $I): void
    {
        $users = UserFactory::createMany(10, ['isVerified' => false]);
        $repository = UserFactory::repository();

        $I->runSymfonyConsoleCommand('app:purge-registration', ['--delete' => true, '--force' => true]);

        foreach ($users as $user) {
            $repository->assert()->notExists([
                'id' => $user->getId(),
            ]);
        }
    }
}
