<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository
    ) {}

    // Add product to cart
    public function add(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []); 

        if (!empty($cart[$id])) {
            $cart[$id]++; 
        } else {
            $cart[$id] = 1; 
        }

        $session->set('cart', $cart); 
    }

    // Delete product from cart
    public function remove(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
    }

    // Get cart info
    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        $cartData = [];
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);

            
            if (!$product) {
                continue;
            }

            $cartData[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }

        return $cartData;
    }

    // Calculate total for cart
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getFullCart() as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $total;
    }
}