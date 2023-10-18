<?php

namespace App\Tests\Application;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use App\Tests\Support\ApplicationTester;

class SearchCest
{
    public function noSearch(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        AdvertisementFactory::createOne();

        $I->amOnPage('/advertisement');
        $I->seeResponseCodeIsSuccessful();
        $I->see(' Résultats: 1');

        $I->see('Liste de petites annonces');
    }

    public function searchNoResults(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        AdvertisementFactory::createOne();

        $I->amOnPage('/advertisement?search=velo');
        $I->seeResponseCodeIsSuccessful();

        $I->see(' Résultats: 0');

        $I->see('Resultat de la recherche');
    }

    public function search(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        AdvertisementFactory::createOne(['title' => 'Apagnan quoicoube quoicoube']);
        AdvertisementFactory::createOne(['title' => 'quoicoube quoicoube feur quoi feur']);

        $I->amOnPage('/advertisement?search=Apagnan');
        $I->seeResponseCodeIsSuccessful();

        $I->see(' Résultats: 1');

        $I->see('Resultat de la recherche');
    }
}
