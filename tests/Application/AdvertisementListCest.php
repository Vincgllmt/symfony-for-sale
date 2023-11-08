<?php

namespace App\Tests\Application;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use App\Tests\Support\ApplicationTester;

class AdvertisementListCest
{
    private $user;

    public function _before()
    {
        $this->user = UserFactory::createOne(['email' => 'yoda@exemple.com', 'password' => 'luke']);
    }

    public function emptyList(ApplicationTester $I)
    {
        $I->amOnPage('/advertisement');
        $I->seeResponseCodeIsSuccessful();
    }

    public function listOf15(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        AdvertisementFactory::createMany(15, ['owner' => $this->user]);
        $I->amOnPage('/advertisement');
        $I->see('1', '.current');
        $I->seeElement('.page');
        $I->seeElement('.last');
        $I->seeNumberOfElements('main .list-group .list-group-item', 10/* only 10, pagination ftw */);
    }
}
