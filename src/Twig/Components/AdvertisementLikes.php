<?php

namespace App\Twig\Components;

use App\Entity\Advertisement;
use App\Entity\User;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class AdvertisementLikes
{
    use DefaultActionTrait;
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

        return $this->adv->getLikes()->contains($this->user);
    }

    #[LiveAction]
    public function toggleLike(): void
    {
        if (!$this->user) {
            return;
        }

        if ($this->isLikedByUser()) {
            $this->adv->removeLike($this->user);
        } else {
            $this->adv->addLike($this->user);
        }
    }
}
