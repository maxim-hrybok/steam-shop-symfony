<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        // Read parameters from URL: ?page=1&search=Dota&categories[]=1
        $page = $request->query->getInt('page', 1);
        $search = $request->query->getString('search', '');
        $selectedCategories = $request->query->all('categories'); // Read array of categories

        $limit = 6; // Products per page

        // Get filtered products based on search and selected categories
        $paginator = $productRepository->getFilteredProducts($page, $limit, $search, $selectedCategories);
        
        // Calculate the total number of pages (Rounding up: total products / limit)
        $totalPages = ceil(count($paginator) / $limit);

        return $this->render('home/index.html.twig', [
            'products' => $paginator,
            'allCategories' => $categoryRepository->findAll(),
            'selectedCategories' => $selectedCategories,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}