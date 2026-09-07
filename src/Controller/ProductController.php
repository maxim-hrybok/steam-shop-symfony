<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(
        Product $product, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        CommentRepository $commentRepository
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                return $this->redirectToRoute('app_login'); // Защита
            }

            // Set the product and user for the comment
            $comment->setProduct($product);
            $comment->setUser($this->getUser());
            $comment->setStatus('pending'); // По умолчанию отправляем на модерацию
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Ваш комментарий отправлен на модерацию!');
            
            // Redirect to the same product page to avoid form resubmission
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        // Fetch only approved comments for the product, ordered by creation date descending
        $approvedComments = $commentRepository->findBy(
            ['product' => $product, 'status' => 'approved'],
            ['createdAt' => 'DESC']
        );

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form, // Pass the form to the template
            'comments' => $approvedComments,
        ]);
    }
}