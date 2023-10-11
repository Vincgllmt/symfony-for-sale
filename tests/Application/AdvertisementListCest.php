<?php

namespace App\Tests\Application;

use App\Tests\Support\ApplicationTester;

class AdvertisementListCest
{
    public function emptyList(ApplicationTester $I)
    {
        $I->amOnPage('/advertisement');
        $I->seeResponseCodeIsSuccessful();
    }
}
