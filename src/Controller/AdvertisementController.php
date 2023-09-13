<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Form\AdvertisementType;
use App\Repository\AdvertisementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdvertisementController extends AbstractController
{
    #[Route('/advertisement', name: 'app_advertisement')]
    public function index(AdvertisementRepository $advertisementRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $advertisementRepository->queryAllByDate(),
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('advertisement/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/advertisement/{id}', name: 'app_advertisement_show', requirements: ['id' => '\d+'])]
    public function show(Advertisement $advertisement): Response
    {
        return $this->render('advertisement/show.html.twig', [
            'advertisement' => $advertisement,
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/advertisement/new', name: 'app_advertisement_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdvertisementType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $advertisement = $form->getData();

            $entityManager->persist($advertisement);
            $entityManager->flush();

            return $this->redirectToRoute('app_advertisement_show', [
                'id' => $advertisement->getId(),
            ]);
        }

        return $this->render('advertisement/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/advertisement/edit/{id}', name: 'app_advertisement_edit', requirements: ['id' => '\d+'])]
    public function edit(Advertisement $advertisement, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdvertisementType::class, $advertisement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $advertisement = $form->getData();

            $entityManager->persist($advertisement);
            $entityManager->flush();

            return $this->redirectToRoute('app_advertisement_show', [
                'id' => $advertisement->getId(),
            ]);
        }

        return $this->render('advertisement/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
