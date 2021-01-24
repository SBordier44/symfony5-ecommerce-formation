<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{

    #[Route('/admin/category/create', name: 'category_create')]
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())->toString()));
            $em->persist($category);
            $em->flush();
        }

        return $this->render(
            'category/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    public function edit(
        int $id,
        CategoryRepository $categoryRepository,
        Request $request,
        EntityManagerInterface $em
    ) {
        $category = $categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'category' => $category,
                'form' => $form->createView()
            ]
        );
    }
}
