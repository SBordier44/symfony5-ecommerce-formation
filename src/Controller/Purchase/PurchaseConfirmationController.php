<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseConfirmationController extends AbstractController
{

    public function __construct(protected CartService $cartService, protected PurchasePersister $purchasePersister)
    {
    }

    /**
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour passer une commande")
     * @param Request $request
     * @return Response
     */
    #[Route('/purchase/confirm', name: 'purchase_confirm')]
    public function confirm(
        Request $request
    ): Response {
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation de commande');
            return $this->redirectToRoute('cart_show');
        }

        if (count($this->cartService->getCartItems()) === 0) {
            $this->addFlash('warning', 'Veuillez remplir votre panier pour pouvoir passer commande');
            return $this->redirectToRoute('homepage');
        }

        $purchase = $form->getData();

        $this->purchasePersister->storePurchase($purchase);

        return $this->redirectToRoute(
            'purchase_payment_form',
            [
                'id' => $purchase->getId()
            ]
        );
    }
}
