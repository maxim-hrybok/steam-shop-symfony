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
    public function add(int $id): bool
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return false;
        }

        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []); 
        
        $currentQty = $cart[$id] ?? 0;

       
        if ($currentQty + 1 > $product->getStock()) {
            
            /** @disregard */
            $session->getFlashBag()->add('error', 'Only ' . $product->getStock() . ' pcs of product "' . $product->getName() . '" are available.');
            return false;
        }

        $cart[$id] = $currentQty + 1; 
        $session->set('cart', $cart); 
        
        // Successfully added to cart
        /** @disregard */
        $session->getFlashBag()->add('success', 'Product added to cart!');
        return true;
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