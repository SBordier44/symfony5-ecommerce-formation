<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductShowEvent;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', priority: -1)]
    public function category(
        string $slug,
        CategoryRepository $categoryRepository
    ): Response {
        $category = $categoryRepository->findOneBy(
            [
                'slug' => $slug
            ]
        );

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render(
            'product/category.html.twig',
            [
                'slug' => $slug,
                'category' => $category
            ]
        );
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show', priority: -1)]
    public function show(
        string $slug,
        ProductRepository $productRepository,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $product = $productRepository->findOneBy(
            [
                'slug' => $slug
            ]
        );

        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        $eventDispatcher->dispatch(new ProductShowEvent($product), ProductShowEvent::EVENT_SHOW);

        return $this->render(
            'product/show.html.twig',
            [
                'product' => $product
            ]
        );
    }

    #[Route('/admin/product/create', name: 'product_create')]
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())->toString()));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute(
                'product_show',
                [
                    'category_slug' => $product->getCategory()->getSlug(),
                    'slug' => $product->getSlug()
                ]
            );
        }

        return $this->render(
            'product/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit(
        int $id,
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $em
    ) {
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute(
                'product_show',
                [
                    'category_slug' => $product->getCategory()->getSlug(),
                    'slug' => $product->getSlug()
                ]
            );
        }

        return $this->render(
            'product/edit.html.twig',
            [
                'product' => $product,
                'form' => $form->createView()
            ]
        );
    }
}
