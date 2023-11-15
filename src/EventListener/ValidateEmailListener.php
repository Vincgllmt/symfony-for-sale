<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener('kernel.request', method: 'onKernelRequest')]
class ValidateEmailListener
{
    public function __construct(private readonly Security $security, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $current_route = $request->attributes->get('_route');

        if ('app_validate_email' === $current_route || 'app_logout' === $current_route || 'app_verify_email' === $current_route) {
            return;
        }

        $user = $this->security->getUser();

        if (null === $user) {
            return;
        }

        if (!$user->isVerified()) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_validate_email')));
        }
    }
}
