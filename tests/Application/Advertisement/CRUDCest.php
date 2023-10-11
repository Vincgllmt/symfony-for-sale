<?php

namespace App\Tests\Application\Advertisement;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use App\Tests\Support\ApplicationTester;

class CRUDCest
{
    public function create(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        $I->amOnPage('/advertisement/new');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('advertisement[title]', 'Le test enfin son blaze dans le crew');
        $I->fillField('advertisement[description]', 'Le test est un test visant a tester le test du test ');
        $I->fillField('advertisement[price]', '100');
        $I->fillField('advertisement[location]', 'La ville du test');
        $I->click('Save');
        $I->seeResponseCodeIsSuccessful();
        $I->amOnPage('/advertisement');
        $I->seeNumberOfElements('main .list-group .list-group-item', 1);
    }

    public function read(ApplicationTester $I)
    {
        CategoryFactory::createOne(['name' => 'quatreger']);
        $adv = AdvertisementFactory::createOne(['title' => 'test', 'description' => 'test', 'price' => 100, 'location' => 'test']);

        $I->amOnPage("/advertisement/{$adv->getId()}");
        $I->seeResponseCodeIsSuccessful();

        $I->see('test', 'h2');
        $I->see('quatreger', '.category-title a');
        $I->see('Prix : 100 €', 'p');
    }

    public function update(ApplicationTester $I)
    {
        CategoryFactory::createOne(['name' => 'quatreger']);
        $adv = AdvertisementFactory::createOne(['title' => 'test', 'description' => 'test', 'price' => 100, 'location' => 'test']);

        $I->amOnPage("/advertisement/edit/{$adv->getId()}");
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('advertisement[title]', 'Fly you fools !');
        $I->fillField('advertisement[description]', 'I have brought peace, freedom, justice, and security to my new empire.');

        $I->click('Save');

        $I->amOnPage("/advertisement/{$adv->getId()}");
        $I->seeResponseCodeIsSuccessful();
        $I->see('I have brought peace, freedom, justice, and security to my new empire.', 'p');
    }
}
