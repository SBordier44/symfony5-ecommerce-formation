<?php

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected MailerInterface $mailer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $event)
    {
        $purchase = $event->getPurchase();
        $email = new TemplatedEmail();

        $email->from(new Address('no-reply@symshop.io', 'SymShop'))
            ->to(new Address($purchase->getOwner()->getEmail(), $purchase->getOwner()->getFullName()))
            ->subject("Bravo, votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context(
                [
                    'purchase' => $purchase
                ]
            );

        $this->mailer->send($email);

        $this->logger->info("Email de confirmation de commande #{$purchase->getId()} envoyé");
    }
}
