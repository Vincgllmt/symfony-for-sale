<?php

namespace App\Tests\Application;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use App\Tests\Support\ApplicationTester;
use Codeception\Attribute\DataProvider;
use Codeception\Attribute\Group;
use Codeception\Example;

class AvailabilityCest
{
    public function _before(ApplicationTester $I)
    {
        $catg = CategoryFactory::createOne();
        $user = UserFactory::createOne(['email' => 'yoda@exemple.com', 'password' => 'luke']);
        AdvertisementFactory::createOne(['category' => $catg, 'owner' => $user]);
    }

    #[Group('available')]
    #[DataProvider('pageIsAvailableProvider')]
    public function pageIsAvailable(ApplicationTester $I, Example $example)
    {
        $I->amOnPage($example[0]);
        $I->seeResponseCodeIsSuccessful();
    }

    public function pageIsAvailableProvider()
    {
        return [
            ['/advertisement'],
            ['/advertisement/new'],
            ['/advertisement/1'],
            ['/advertisement/user/1'],
            ['/advertisement/edit/1'],
            ['/advertisement/delete/1'],
            ['/category'],
            ['/category/1'],
            ['/login'],
            ['/logout'],
            ['/register'],
            ['/validate/email'],
            ['/verify/email'],
        ];
    }
}
