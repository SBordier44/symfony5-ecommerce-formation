<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchasesListController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    #[Route('/purchases', name: 'purchase_index')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render(
            'purchase/index.html.twig',
            [
                'purchases' => $user->getPurchases()
            ]
        );
    }
}
