<?php

declare(strict_types=1);

namespace App\Cart;

use App\Entity\Product;

class CartItem
{
    protected Product $product;
    protected int $quantity;

    public function getTotal(): int
    {
        return $this->product->getPrice() * $this->quantity;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): CartItem
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): CartItem
    {
        $this->quantity = $quantity;
        return $this;
    }
}
