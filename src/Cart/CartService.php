<?php

declare(strict_types=1);

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    public function __construct(protected SessionInterface $session, protected ProductRepository $productRepository)
    {
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart): void
    {
        $this->session->set('cart', $cart);
    }

    public function clear(): void
    {
        $this->saveCart([]);
    }

    public function add(int $id): void
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;

        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $productId => $quantity) {
            $product = $this->productRepository->find($productId);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $quantity;
        }

        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getCartItems(): array
    {
        $cartItems = [];

        foreach ($this->getCart() as $productId => $quantity) {
            $product = $this->productRepository->find($productId);

            if (!$product) {
                continue;
            }

            $cartItems[] = (new CartItem())
                ->setProduct($product)
                ->setQuantity($quantity);
        }
        return $cartItems;
    }

    public function remove(int $id): void
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id): void
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->saveCart($cart);
    }

    public function increment(int $id): void
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        $cart[$id]++;

        $this->saveCart($cart);
    }
}
