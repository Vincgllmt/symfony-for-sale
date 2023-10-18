<?php

namespace App\Tests\Application;

use App\Tests\Support\ApplicationTester;

class HomeCest
{
    public function _before(ApplicationTester $I)
    {
    }

    // tests
    public function redirect(ApplicationTester $I): void
    {
        $I->amOnPage('/');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Liste de petites annonces', 'h1');
    }
}
