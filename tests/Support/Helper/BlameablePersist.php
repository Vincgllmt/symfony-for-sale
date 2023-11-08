<?php

declare(strict_types=1);

namespace App\Tests\Support\Helper;

use Codeception\Exception\ModuleException;
use Codeception\Module;
use Codeception\Module\Symfony;
use Codeception\TestInterface;

class BlameablePersist extends Module
{
    /**
     * Executed before tests.
     *
     * Persist Blameable listener as permanent service to fix issue where user is null when blameable is called
     */
    public function _before(TestInterface $test): void
    {
        parent::_before($test);

        try {
            /** @var Symfony $symfonyModule */
            $symfonyModule = $this->getModule('Symfony');
            $symfonyModule->persistPermanentService('stof_doctrine_extensions.listener.blameable');
        } catch (ModuleException) {
        }
    }
}
