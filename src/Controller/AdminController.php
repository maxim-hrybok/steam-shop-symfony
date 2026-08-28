<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//gain access to the request object
use Symfony\Component\HttpFoundation\Request;

//for product management
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin')] //This atribute will prefix all routes in this controller with /admin
final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_index')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    //Page for creating a new product
    #[Route('/create', name:'app_admin_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();

        //crete form and link it to empty object $product
        $form = $this->createForm(ProductType::class, $product);

        //
        $form -> handleRequest($request);

        //if form is send and valided (CSRF checked and no erreors)
        if ($form->isSubmitted() && $form->isValid()) {

        //Save object into DB
        $entityManager->persist($product);
        $entityManager->flush();

        //redirect back to products page
        return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/form.html.twig', [
            'form' => $form,
        ]);
    }
}
