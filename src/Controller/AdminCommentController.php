<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/comments')]
#[IsGranted('ROLE_ADMIN')] 
class AdminCommentController extends AbstractController
{
    // 1. List all pending comments for moderation
    #[Route('/', name: 'app_admin_comments_index')]
    public function index(CommentRepository $commentRepository): Response
    {
    
        $pendingComments = $commentRepository->findBy(['status' => 'pending'], ['createdAt' => 'ASC']);

        return $this->render('admin/comments/index.html.twig', [
            'comments' => $pendingComments,
        ]);
    }

    // 2. set comment status to 'approved'
    #[Route('/{id}/approve', name: 'app_admin_comments_approve', methods: ['POST'])]
    public function approve(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve'.$comment->getId(), $request->request->get('_token'))) {
            $comment->setStatus('approved');
            $entityManager->flush(); // Сохраняем новый статус в базу
            
            $this->addFlash('success', 'Комментарий одобрен и теперь виден на сайте.');
        }

        return $this->redirectToRoute('app_admin_comments_index');
    }

    // 3. delete comment
    #[Route('/{id}/delete', name: 'app_admin_comments_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush(); // Удаляем из базы
            
            $this->addFlash('success', 'Комментарий отклонён и удалён.');
        }

        return $this->redirectToRoute('app_admin_comments_index');
    }
}