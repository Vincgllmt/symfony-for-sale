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

        $out = $I->runSymfonyConsoleCommand('app:purge-registration', ['--delete' => true, '--force' => true], ['']);

        $I->assertStringContainsString('[OK] 10 users have been deleted.', $out);

        foreach ($users as $user) {
            $repository->assert()->notExists([
                'id' => $user->getId(),
            ]);
        }
    }

    public function purgeRegistrationWithDay(ApplicationTester $I): void
    {
        $usersNotToDelete = UserFactory::createMany(10, ['isVerified' => false, 'registeredAt' => new \DateTime('-4 days')]);
        $usersToDelete = UserFactory::createMany(10, ['isVerified' => false, 'registeredAt' => new \DateTime('-1 days')]);
        $repository = UserFactory::repository();

        $out = $I->runSymfonyConsoleCommand('app:purge-registration', ['--delete' => true, '--force' => true, 'days' => 2], ['']);
        $I->assertStringContainsString('[OK] 10 users have been deleted.', $out);
        foreach ($usersToDelete as $user) {
            $repository->assert()->notExists([
                'id' => $user->getId(),
            ]);
        }

        foreach ($usersNotToDelete as $user) {
            $repository->assert()->exists([
                'id' => $user->getId(),
            ]);
        }
    }
}
