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

use App\Service\FileUploader;

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
    public function create(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $product = new Product();

        //crete form and link it to empty object $product
        $form = $this->createForm(ProductType::class, $product);

        //
        $form -> handleRequest($request);

        //if form is send and valided (CSRF checked and no erreors)
        if ($form->isSubmitted() && $form->isValid()) {

        // 1. get file from the form (it named image as we named it in productTipe)
        $imageFile = $form->get('image')->getData();

        // 2. if file is submited we send it to service
        if ($imageFile) {
            $imageFileName = $fileUploader->upload($imageFile);
            $product->setImageFilename($imageFileName); // Записываем имя файла в Entity
        }
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

    #[Route('/edit/{id}', name: 'app_admin_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Form is automatically populated with the existing product data because of the Product $product parameter in the method signature.
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        // 1. get file from the form (it named image as we named it in productTipe)
        $imageFile = $form->get('image')->getData();

        // 2. if file is submited we send it to service
        if ($imageFile) {
            $imageFileName = $fileUploader->upload($imageFile);
            $product->setImageFilename($imageFileName); // Записываем имя файла в Entity
        }
            // no persist(), as Doctrine already knows about this object.
            // only flush() is needed to save the changes.
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/form.html.twig', [
            'form' => $form,
        ]);
    }

    // Delete product , restrict to POST method to prevent accidental deletion via GET requests
    #[Route('/delete/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Check CSRF token for security 
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product); // Говорим Doctrine удалить объект
            $entityManager->flush();          // Выполняем SQL запрос DELETE
        }

        return $this->redirectToRoute('app_admin_index');
    }
}
