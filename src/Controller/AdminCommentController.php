<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminCommentController extends AbstractController
{
    #[Route('/admin/comment', name: 'app_admin_comment')]
    public function index(): Response
    {
        return $this->render('admin_comment/index.html.twig', [
            'controller_name' => 'AdminCommentController',
        ]);
    }
}
