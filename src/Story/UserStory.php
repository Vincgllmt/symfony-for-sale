<?php

namespace App\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    public function build(): void
    {
        $this->addState('admin', UserFactory::createOne([
            'email' => 'admin@exemple.com',
            'password' => 'admin',
            'firstname' => 'admin',
            'lastname' => 'admin',
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]));
        $this->addState('admin2', UserFactory::createOne([
            'email' => 'admin2@exemple.com',
            'password' => 'admin2',
            'firstname' => 'admin2',
            'lastname' => 'admin2',
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]));
        $this->addState('user', UserFactory::createOne([
            'email' => 'user@exemple.com',
            'password' => 'user',
            'firstname' => 'user',
            'lastname' => 'user',
        ]));
        $this->addState('user2', UserFactory::createOne([
            'email' => 'user2@exemple.com',
            'password' => 'user2',
            'firstname' => 'user2',
            'lastname' => 'user2',
        ]));

        $this->addToPool('users', UserFactory::createMany(10));
        $this->addToPool('unverifiedUsers', UserFactory::createMany(4, ['isVerified' => false]));
    }
}
