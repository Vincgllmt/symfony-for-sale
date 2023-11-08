<?php

namespace App\Security\Voter;

use App\Entity\Advertisement;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AdvertisementVoter extends Voter
{
    public const EDIT = 'ADVERTISSEMENT_EDIT';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::EDIT == $attribute
            && $subject instanceof \App\Entity\Advertisement;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            default => false,
        };
    }

    private function canEdit(Advertisement $subject, UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_ADMIN') || $subject->getOwner()->getId() === $user->getId();
    }
}
