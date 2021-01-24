<?php

declare(strict_types=1);

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(protected ProductRepository $productRepository, protected CartService $cart)
    {
    }

    #[Route('/cart/add/{id}', name: 'cart_add', requirements: ['id' => '\d+'])]
    public function add(
        int $id,
        Request $request
    ): Response {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        $this->cart->add($product->getId());

        $this->addFlash('success', 'Le produit à été ajouté à votre panier');

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart', name: 'cart_show')]
    public function show(): Response
    {
        $form = $this->createForm(CartConfirmationType::class);

        return $this->render(
            'cart/index.html.twig',
            [
                'items' => $this->cart->getCartItems(),
                'total' => $this->cart->getTotal(),
                'confirmationForm' => $form->createView()
            ]
        );
    }

    #[Route('/cart/delete/{id}', name: 'cart_delete', requirements: ['id' => '\d+'])]
    public function delete(
        int $id
    ): Response {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        $this->cart->remove($id);

        $this->addFlash('success', 'Le produit à bien été supprimé de votre panier');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/decrement/{id}', name: 'cart_decrement', requirements: ['id' => '\d+'])]
    public function decrement(
        int $id
    ): Response {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        $this->cart->decrement($id);

        $this->addFlash('success', 'La quantité du produit à bien été modifiée');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/increment/{id}', name: 'cart_increment', requirements: ['id' => '\d+'])]
    public function increment(
        int $id
    ): Response {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        $this->cart->increment($id);

        $this->addFlash('success', 'La quantité du produit à bien été modifiée');

        return $this->redirectToRoute('cart_show');
    }
}
