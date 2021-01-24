<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @param int $id
     * @param PurchaseRepository $purchaseRepository
     * @param CartService $cartService
     * @param EventDispatcherInterface $eventDispatcher
     * @return RedirectResponse
     * @IsGranted("ROLE_USER")
     */
    #[Route('/purchase/terminate/{id}', name: 'purchase_payment_success', requirements: ['id' => '\d+'])]
    public function success(
        int $id,
        PurchaseRepository $purchaseRepository,
        CartService $cartService,
        EventDispatcherInterface $eventDispatcher
    ): RedirectResponse {
        $purchase = $purchaseRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        if (
            !$purchase
            || ($purchase->getStatus() === Purchase::STATUS_PAID)
            || ($purchase->getOwner() !== $this->getUser())
        ) {
            $this->addFlash('warning', "La commande n'existe pas");
            return $this->redirectToRoute('purchase_index');
        }

        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();

        $cartService->clear();

        $eventDispatcher->dispatch(new PurchaseSuccessEvent($purchase), 'purchase.success');

        $this->addFlash('success', 'La commande a été payée et confirmée avec succès !');

        return $this->redirectToRoute('purchase_index');
    }
}
