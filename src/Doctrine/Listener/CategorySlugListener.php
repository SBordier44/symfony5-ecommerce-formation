<?php

namespace App\Doctrine\Listener;

use App\Entity\Category;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorySlugListener
{
    public function __construct(protected SluggerInterface $slugger)
    {
    }

    public function prePersist(Category $category, LifecycleEventArgs $event): void
    {
        if (empty($category->getSlug())) {
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
        }
    }
}
