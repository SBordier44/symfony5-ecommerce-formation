<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController
{
    /**
     * @param int $id
     * @param PurchaseRepository $repository
     * @param StripeService $stripeService
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    #[Route('/purchase/pay/{id}', name: 'purchase_payment_form', requirements: ['id' => '\d+'])]
    public function showCardForm(
        int $id,
        PurchaseRepository $repository,
        StripeService $stripeService
    ): Response {
        $purchase = $repository->find($id);

        if (
            !$purchase
            || ($purchase->getStatus() === Purchase::STATUS_PAID)
            || ($purchase->getOwner() !== $this->getUser())
        ) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->render(
            'purchase/payment.html.twig',
            [
                'clientSecret' => $stripeService->getPaymentIntent($purchase)->client_secret,
                'publicKey' => $stripeService->getPublicKey(),
                'purchase' => $purchase
            ]
        );
    }
}
