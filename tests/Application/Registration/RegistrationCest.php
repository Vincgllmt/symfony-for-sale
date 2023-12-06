<?php

namespace App\Tests\Application\Registration;

use App\Tests\Support\ApplicationTester;

class RegistrationCest
{
    public function testRegistration(ApplicationTester $I): void
    {
        $I->amOnPage('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('S\'enregistrer', 'h1');
        $I->fillField('registration_form[email]', 'kylo.ren@starkiller.espace');
        $I->fillField('registration_form[lastName]', 'Ren');
        $I->fillField('registration_form[firstName]', 'Kylo');
        $I->fillField('registration_form[plainPassword][first]', 'a16z::,,llmA');
        $I->fillField('registration_form[plainPassword][second]', 'a16z::,,llmA');
        $I->click('S\'enregistrer');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Valider le compte: Ren Kylo', 'h1');
    }

    public function testRegistrationWithInvalidData(ApplicationTester $I): void
    {
        $I->amOnPage('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('S\'enregistrer', 'h1');
        $I->fillField('registration_form[email]', 'a');
        $I->fillField('registration_form[lastName]', 'a');
        $I->fillField('registration_form[firstName]', 'a');
        $I->fillField('registration_form[plainPassword][first]', 'a');
        $I->fillField('registration_form[plainPassword][second]', 'a');
        $I->click('S\'enregistrer');

        $I->seeResponseCodeIsSuccessful();
        $I->see('Le formulaire contient des erreurs.');
    }

    public function testRegistrationWithInvalidPassword(ApplicationTester $I): void
    {
        $I->amOnPage('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('S\'enregistrer', 'h1');
        $I->fillField('registration_form[email]', 'kylo.ren@starkiller.espace');
        $I->fillField('registration_form[lastName]', 'Ren');
        $I->fillField('registration_form[firstName]', 'Kylo');
        $I->fillField('registration_form[plainPassword][first]', 'a16z::,,llmA');
        $I->fillField('registration_form[plainPassword][second]', 'a16z::,,llmAb');

        $I->click('S\'enregistrer');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Le formulaire contient des erreurs.');
    }

    public function testRegistrationWithInvalidSecondPassword(ApplicationTester $I): void
    {
        $I->amOnPage('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('S\'enregistrer', 'h1');
        $I->fillField('registration_form[email]', 'kylo.ren@starkiller.espace');
        $I->fillField('registration_form[lastName]', 'Ren');
        $I->fillField('registration_form[firstName]', 'Kylo');
        $I->fillField('registration_form[plainPassword][first]', 'abc');
        $I->fillField('registration_form[plainPassword][second]', 'abc');
        $I->click('S\'enregistrer');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Le formulaire contient des erreurs.');
    }

    public function testRegistrationEmail(ApplicationTester $I): void
    {
        $I->amOnPage('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('S\'enregistrer', 'h1');
        $I->fillField('registration_form[email]', 'kylo.ren@starkiller.espace');
        $I->fillField('registration_form[lastName]', 'Ren');
        $I->fillField('registration_form[firstName]', 'Kylo');
        $I->fillField('registration_form[plainPassword][first]', 'a16z::,,llmA');
        $I->fillField('registration_form[plainPassword][second]', 'a16z::,,llmA');

        $I->click('S\'enregistrer');

        $I->seeResponseCodeIsSuccessful();
        $I->see('Valider le compte: Ren Kylo', 'h1');

        $I->stopFollowingRedirects();
        $I->click('Envoyer un mail de confirmation');

        $I->seeEmailIsSent(1);
        $emailHtml = $I->grabLastSentEmail()->getHtmlBody();

        $I->startFollowingRedirects();

        preg_match('/<a href="(.*)">/', $emailHtml, $matches, PREG_OFFSET_CAPTURE);
        $url = parse_url($matches[1][0], PHP_URL_PATH).'?'.parse_url($matches[1][0], PHP_URL_QUERY);
        $I->amOnPage($url);
        $I->seeResponseCodeIsSuccessful();
        $I->see('Votre email a bien été vérifié.');
    }

    public function testRegistrationRedirectToVerifyEmail(ApplicationTester $I): void
    {
        $I->amOnPage('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('S\'enregistrer', 'h1');
        $I->fillField('registration_form[email]', 'kylo.ren@starkiller.espace');
        $I->fillField('registration_form[lastName]', 'Ren');
        $I->fillField('registration_form[firstName]', 'Kylo');
        $I->fillField('registration_form[plainPassword][first]', 'a16z::,,llmA');
        $I->fillField('registration_form[plainPassword][second]', 'a16z::,,llmA');

        $I->click('S\'enregistrer');

        $I->seeResponseCodeIsSuccessful();
        $I->see('Valider le compte: Ren Kylo', 'h1');

        $I->amOnPage('/');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/validate/email');
    }
}
