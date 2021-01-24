<?php

namespace App\Doctrine\Listener;

use App\Entity\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener
{
    public function __construct(protected SluggerInterface $slugger)
    {
    }

    public function prePersist(Product $product, LifecycleEventArgs $event): void
    {
        if (empty($product->getSlug())) {
            $product->setSlug(strtolower($this->slugger->slug($product->getName())));
        }
    }
}
