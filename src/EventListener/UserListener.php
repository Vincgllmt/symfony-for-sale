<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener('kernel.request', method: 'onKernelRequest')]
class UserListener
{
}
