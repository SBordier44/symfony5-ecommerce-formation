<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductShowEvent extends Event
{
    public const EVENT_SHOW = 'product.show';

    public function __construct(protected Product $product)
    {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
