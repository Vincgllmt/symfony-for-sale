<?php

namespace App\Tests\Application\Security;

use App\Factory\UserFactory;
use App\Tests\Support\ApplicationTester;

class AuthenticationCest
{
    public function login(ApplicationTester $I): void
    {
        $user = UserFactory::createOne([
            'email' => 'yoda@exemple.com',
            'password' => '123456',
        ]);

        $I->amOnPage('/login');
        $I->seeResponseCodeIs(200);
        $I->see('Veuillez vous connecter', 'h1');

        $I->submitForm('#login-form', [
            '_username' => $user->getEmail(),
            '_password' => '123456',
        ]);

        $I->seeResponseCodeIsSuccessful();
        $I->canSeePageIsAvailable('/');
        $I->seeAuthentication();
    }
}
