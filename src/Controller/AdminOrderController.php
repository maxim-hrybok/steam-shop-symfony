<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/orders')]
#[IsGranted('ROLE_ADMIN')] 
class AdminOrderController extends AbstractController
{
    // all orders page for admin
    #[Route('/', name: 'app_admin_orders_index')]
    public function index(OrderRepository $orderRepository): Response
    {
        // filter orders by createdAt descending (latest first)
        $orders = $orderRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    // detailed view and status change (ParamConverter will find the Order by {id})
    #[Route('/{id}/edit', name: 'app_admin_orders_edit', methods: ['GET', 'POST'])]
    public function edit(Order $order, Request $request, EntityManagerInterface $entityManager): Response
    {
        // If a POST request is received with the status update form
        if ($request->isMethod('POST')) {
            $newStatus = $request->request->get('status');
            $csrfToken = $request->request->get('_token');

            // check if the CSRF token is valid before updating the order status
            if ($this->isCsrfTokenValid('update_status' . $order->getId(), $csrfToken)) {
                
                $order->setStatus($newStatus); 
                $entityManager->flush();       
                
                
                $this->addFlash('success', 'Order status for order #' . $order->getId() . ' has been updated successfully!');
                
                return $this->redirectToRoute('app_admin_orders_edit', ['id' => $order->getId()]);
            }
        }

        return $this->render('admin/orders/edit.html.twig', [
            'order' => $order,
        ]);
    }
}