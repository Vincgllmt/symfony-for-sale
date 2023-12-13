<?php

namespace App\Twig\Components;

use App\Entity\Advertisement;
use App\Entity\User;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
final class AdvertisementLikes
{
    public Advertisement $adv;
    public ?User $user;

    public function getLikesCount(): int
    {
        return $this->adv->getLikes()->count();
    }

    public function isLikedByUser(): bool
    {
        if (!$this->user) {
            return false;
        }

        return $this->adv->getLikes()->contains($this->user->getId());
    }
}
