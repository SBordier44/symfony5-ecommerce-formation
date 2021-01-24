<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    public function __construct(
        protected Security $security,
        protected CartService $cartService,
        protected EntityManagerInterface $em
    ) {
    }

    public function storePurchase(Purchase $purchase): void
    {
        $purchase->setOwner($this->security->getUser());

        foreach ($this->cartService->getCartItems() as $cartItem) {
            $purchaseItem = (new PurchaseItem())
                ->setPurchase($purchase)
                ->setProduct($cartItem->getProduct())
                ->setProductName($cartItem->getProduct()->getName())
                ->setProductPrice($cartItem->getProduct()->getPrice())
                ->setQuantity($cartItem->getQuantity())
                ->setTotal($cartItem->getTotal());

            $this->em->persist($purchaseItem);
        }

        $this->em->persist($purchase);
        $this->em->flush();
    }
}
