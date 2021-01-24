<?php

namespace App\Event;

use App\Entity\Purchase;
use Symfony\Contracts\EventDispatcher\Event;

class PurchaseSuccessEvent extends Event
{
    public function __construct(protected Purchase $purchase)
    {
    }

    public function getPurchase(): Purchase
    {
        return $this->purchase;
    }
}
