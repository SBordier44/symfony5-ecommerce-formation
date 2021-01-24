<?php

namespace App\EventSubscriber;

use App\Event\ProductShowEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductShowLogSubscriber implements EventSubscriberInterface
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    public function onProductShow(ProductShowEvent $event): void
    {
        $this->logger->info('Consultation du produit #' . $event->getProduct()->getId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductShowEvent::EVENT_SHOW => 'onProductShow',
        ];
    }
}
