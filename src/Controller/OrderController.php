<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/orders')]
// Force all routes in this controller to require the user to be logged in and have ROLE_USER
#[IsGranted('ROLE_USER')] 
class OrderController extends AbstractController
{
    // page with all orders of the current user
    #[Route('/', name: 'app_orders_index')]
    public function index(): Response
    {
        // User alreay have this method ( getOrders() )!
        /** @disregard */
        $orders = $this->getUser()->getOrders();
        

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    // Checkout
    #[Route('/checkout', name: 'app_orders_checkout', methods: ['POST'])]
    public function checkout(CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        $cartItems = $cartService->getFullCart();

        if (empty($cartItems)) {
            return $this->redirectToRoute('app_cart_index');
        }

        // 1. Create a new Order entity and set its properties
        $order = new Order();
        $order->setUser($this->getUser()); // get current logged-in user
        $order->setTotalPrice($cartService->getTotal());
        $order->setStatus('pending'); // Set status to pending 
        $order->setCreatedAt(new \DateTimeImmutable());

        // 2. Create order items
         foreach ($cartItems as $item) {
            $product = $item['product'];
            $quantityToBuy = $item['quantity'];

            
            if ($product->getStock() < $quantityToBuy) {
                $this->addFlash('error', 'Sorry, product "' . $product->getName() . '" is out of stock.');
                return $this->redirectToRoute('app_cart_index');
            }

           
            $orderItem = new OrderItem();
            $orderItem->setPurchaseOrder($order);
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantityToBuy);
            $orderItem->setPrice($product->getPrice());
            
            
            $product->setStock($product->getStock() - $quantityToBuy);

            
            $entityManager->persist($product);
            $entityManager->persist($orderItem);
        }

        $entityManager->persist($order);
        
        
        $entityManager->flush();

        // 3. Clear the cart after successful checkout
        $session = $cartService->getFullCart(); 
        foreach($session as $item) {
           $cartService->remove($item['product']->getId());
        }

        
        return $this->redirectToRoute('app_orders_index');
    }
}