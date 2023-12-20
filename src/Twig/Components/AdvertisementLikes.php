<?php

namespace App\Twig\Components;

use App\Entity\Advertisement;
use App\Security\Voter\AdvertisementVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class AdvertisementLikes
{
    use DefaultActionTrait;

    #[LiveProp] public Advertisement $adv;
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }

    public function getLikesCount(): int
    {
        return $this->adv->getLikes()->count();
    }

    public function isLikedByUser(): bool
    {
        $user = $this->security->getUser();
        if (!$user) {
            return false;
        }

        return $this->adv->getLikes()->contains($user);
    }

    #[LiveAction]
    #[IsGranted(AdvertisementVoter::LIKE)]
    public function toggleLike(): void
    {
        $user = $this->security->getUser();
        if ($this->isLikedByUser()) {
            $this->adv->removeLike($user);
        } else {
            $this->adv->addLike($user);
        }

        $this->entityManager->flush();
    }
}
