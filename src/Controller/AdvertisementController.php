<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Repository\AdvertisementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdvertisementController extends AbstractController
{
    #[Route('/advertisement', name: 'app_advertisement')]
    public function index(AdvertisementRepository $advertisementRepository): Response
    {
        $advertisements = $advertisementRepository->findAllByDate();

        return $this->render('advertisement/index.html.twig', [
            'advertisements' => $advertisements,
        ]);
    }

    #[Route('/advertisement/{id}', name: 'app_advertisement_show', requirements: ['id' => '\d+'])]
    public function show(Advertisement $advertisement): Response
    {
        return $this->render('advertisement/show.html.twig', [
            'advertisement' => $advertisement,
        ]);
    }
}
