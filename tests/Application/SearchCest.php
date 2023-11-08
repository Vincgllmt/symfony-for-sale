<?php

namespace App\Tests\Application;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use App\Tests\Support\ApplicationTester;

class SearchCest
{
    private $user;

    public function _before()
    {
        $this->user = UserFactory::createOne(['email' => 'yoda@exemple.com', 'password' => 'luke']);
    }

    public function noSearch(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        AdvertisementFactory::createOne(['owner' => $this->user]);

        $I->amOnPage('/advertisement');
        $I->seeResponseCodeIsSuccessful();
        $I->see(' Résultats: 1');

        $I->see('Liste de petites annonces');
    }

    public function searchNoResults(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        AdvertisementFactory::createOne(['owner' => $this->user]);

        $I->amOnPage('/advertisement?search=velo');
        $I->seeResponseCodeIsSuccessful();

        $I->see(' Résultats: 0');

        $I->see('Resultat de la recherche');
    }

    public function search(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        AdvertisementFactory::createOne(['title' => 'Apagnan quoicoube quoicoube', 'owner' => $this->user]);
        AdvertisementFactory::createOne(['title' => 'quoicoube quoicoube feur quoi feur', 'owner' => $this->user]);

        $I->amOnPage('/advertisement?search=Apagnan');
        $I->seeResponseCodeIsSuccessful();

        $I->see(' Résultats: 1');

        $I->see('Resultat de la recherche');
    }
}
