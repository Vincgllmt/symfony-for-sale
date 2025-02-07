<?php

namespace App\Tests\Application\Advertisement;

use App\Factory\AdvertisementFactory;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use App\Tests\Support\ApplicationTester;
use Codeception\Util\HttpCode;

class CRUDCest
{
    private $user;

    public function _before(): void
    {
        $this->user = UserFactory::createOne(['email' => 'yoda@exemple.com', 'password' => 'luke', 'isVerified' => 'true']);
    }

    public function create(ApplicationTester $I)
    {
        $I->amLoggedInAs($this->user->object());
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
        $adv = AdvertisementFactory::createOne(['title' => 'test', 'description' => 'test', 'price' => 100, 'location' => 'test', 'owner' => $this->user]);

        $I->amOnPage("/advertisement/{$adv->getId()}");
        $I->seeResponseCodeIsSuccessful();

        $I->see('test', 'h2');
        $I->see('quatreger', '.category-title a');
        $I->see('Prix : 100 €', 'p');
    }

    public function update(ApplicationTester $I)
    {
        CategoryFactory::createOne(['name' => 'quatreger']);
        $adv = AdvertisementFactory::createOne(['title' => 'test', 'description' => 'test', 'price' => 100, 'location' => 'test', 'owner' => $this->user]);

        $I->amLoggedInAs($this->user->object());
        $I->amOnPage("/advertisement/edit/{$adv->getId()}");
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('advertisement[title]', 'Fly you fools !');
        $I->fillField('advertisement[description]', 'I have brought peace, freedom, justice, and security to my new empire.');

        $I->click('Save');

        $I->amOnPage("/advertisement/{$adv->getId()}");
        $I->seeResponseCodeIsSuccessful();
        $I->see('I have brought peace, freedom, justice, and security to my new empire.', 'p');
    }

    public function cantUpdateNotOwned(ApplicationTester $I)
    {
        CategoryFactory::createOne(['name' => 'quatreger']);
        $user2 = UserFactory::createOne(['email' => 'atrain@the7.com', 'password' => 'luke']);
        $adv = AdvertisementFactory::createOne(['title' => 'test', 'description' => 'test', 'price' => 100, 'location' => 'test', 'owner' => $user2]);

        $I->amLoggedInAs($this->user->object());
        $I->amOnPage("/advertisement/edit/{$adv->getId()}");
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    public function delete(ApplicationTester $I)
    {
        CategoryFactory::createOne(['name' => 'quatreger']);
        $adv = AdvertisementFactory::createOne(['title' => 'feur', 'description' => 'test', 'price' => 100, 'location' => 'test', 'owner' => $this->user]);

        $I->amLoggedInAs($this->user->object());
        $I->amOnPage("/advertisement/delete/{$adv->getId()}");
        $I->seeResponseCodeIsSuccessful();

        $I->click('Supprimer');
        $I->seeResponseCodeIsSuccessful();

        $adv->assertNotPersisted();
    }

    public function cantDeleteNotOwned(ApplicationTester $I)
    {
        CategoryFactory::createOne(['name' => 'quatreger']);
        $user2 = UserFactory::createOne();
        $adv = AdvertisementFactory::createOne(['title' => 'feur', 'description' => 'test', 'price' => 100, 'location' => 'test', 'owner' => $user2]);

        $I->amLoggedInAs($this->user->object());
        $I->amOnPage("/advertisement/delete/{$adv->getId()}");
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    public function createFailedIfNotLogged(ApplicationTester $I)
    {
        CategoryFactory::createOne();
        $I->amOnPage('/advertisement/new');
        $I->canSeePageRedirectsTo('advertisement/new', '/login');
    }
}
