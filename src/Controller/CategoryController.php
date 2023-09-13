<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\AdvertisementRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], orderBy: [
            'name' => 'ASC',
        ]);

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show')]
    public function show(Category $category, PaginatorInterface $paginator, Request $request, AdvertisementRepository $advertisementRepository): Response
    {
        $pagination = $paginator->paginate(
            $advertisementRepository->queryByCategory($category),
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('category/show.html.twig', [
            'pagination' => $pagination,
            'category' => $category,
        ]);
    }
}
