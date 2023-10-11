<?php

namespace App\Tests\Application;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use App\Tests\Support\ApplicationTester;

class AdvertisementListCest
{
    public function emptyList(ApplicationTester $I)
    {
        $I->amOnPage('/advertisement');
        $I->seeResponseCodeIsSuccessful();
    }

    public function listOf15(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        AdvertisementFactory::createMany(15);
        $I->amOnPage('/advertisement');
        $I->seeElement('.current');
        $I->seeElement('.page');
        $I->seeElement('.last');
        $I->seeNumberOfElements('main .list-group .list-group-item', 10/* only 10, pagination ftw */);
    }
}
