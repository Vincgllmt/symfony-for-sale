<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Form\AdvertisementType;
use App\Repository\AdvertisementRepository;
use App\Repository\UserRepository;
use App\Security\Voter\AdvertisementVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdvertisementController extends AbstractController
{
    #[Route('/advertisement', name: 'app_advertisement')]
    public function index(AdvertisementRepository $advertisementRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $advertisementRepository->queryAllByDate($request->query->get('search')),
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('advertisement/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/advertisement/{id}', name: 'app_advertisement_show', requirements: ['id' => '\d+'])]
    public function show(#[MapEntity(expr: 'repository.findOneWithCategory(id)')] Advertisement $advertisement): Response
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
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }
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

        return $this->render('advertisement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted(AdvertisementVoter::EDIT, 'advertisement')]
    #[Route('/advertisement/edit/{id}', name: 'app_advertisement_edit', requirements: ['id' => '\d+'])]
    public function edit(#[MapEntity(expr: 'repository.find(id)')] Advertisement $advertisement, Request $request, EntityManagerInterface $entityManager): Response
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

        return $this->render('advertisement/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted(AdvertisementVoter::EDIT, 'advertisement')]
    #[Route('/advertisement/delete/{id}', name: 'app_advertisement_delete', requirements: ['id' => '\d+'])]
    public function delete(Advertisement $advertisement, EntityManagerInterface $entityManager, Request $request): Response
    {
        $deleteForm = $this->createFormBuilder($advertisement)
            ->add('delete', SubmitType::class, ['label' => 'Supprimer'])
            ->add('cancel', SubmitType::class, ['label' => 'Annuler'])
            ->getForm();
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            /* @var SubmitButton $deleteBtn */
            /* @var SubmitButton $cancelBtn */

            $deleteBtn = $deleteForm->get('delete');
            $cancelBtn = $deleteForm->get('cancel');

            if ($deleteBtn->isClicked()) {
                $entityManager->remove($advertisement);
                $entityManager->flush();

                return $this->redirectToRoute('app_advertisement');
            }

            if ($cancelBtn->isClicked()) {
                return $this->redirectToRoute('app_advertisement_show', [
                    'id' => $advertisement->getId(),
                ]);
            }
        }

        return $this->render('advertisement/delete.html.twig', [
            'advertisement' => $advertisement,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    #[Route('/advertisement/user/{id}', name: 'app_advertisement_user', requirements: ['id' => '\d+'])]
    public function advertisementOfUser(AdvertisementRepository $advertisementRepository, UserRepository $userRepository, PaginatorInterface $paginator, Request $request, int $id): Response
    {
        $user = null;
        $isOwner = false;
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') && $this->getUser()->id == $id) {
            $user = $this->getUser();
            $isOwner = true;
        } else {
            $user = $userRepository->find($id);
        }

        $pagination = $paginator->paginate(
            $advertisementRepository->queryByUser($user->getId()),
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('advertisement/advertisement_user.html.twig', [
            'pagination' => $pagination,
            'isOwner' => $isOwner,
            'user' => $user,
        ]);
    }
}
