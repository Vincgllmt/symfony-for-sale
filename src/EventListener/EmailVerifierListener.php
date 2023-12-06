<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Event\UserConfirmationEmailNotReceived;
use App\Event\UserRegistered;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(method: 'onUserRegistered')]
#[AsEventListener(method: 'onUserConfirmationEmailNotReceived')]
class EmailVerifierListener
{
    public function __construct(
        private readonly EmailVerifier $emailVerifier
    ) {
    }

    public function onUserRegistered(UserRegistered $event): void
    {
        $this->sendEmailConfirmation($event->getUser());
    }

    public function onUserConfirmationEmailNotReceived(UserConfirmationEmailNotReceived $event): void
    {
        $this->sendEmailConfirmation($event->getUser());
    }

    public function sendEmailConfirmation(User $user): void
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
