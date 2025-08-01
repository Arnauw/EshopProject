<?php

namespace App\Controller;

use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Category;
#[Route('/admin')]
final class AdminCategoryController extends AbstractController
{
    #[Route('/categories/show', name: 'app_category_show')]
    public function showCategories(EntityManagerInterface $em,): Response
    {
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/new', name: 'app_category_new')]
    public function createCategory(Request $req, EntityManagerInterface $em,): Response
    {

        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Category created successfully!');
            return $this->redirectToRoute('app_category_show');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/categories/edit/{id}', name: 'app_category_edit')]
    public function editCategory(Category $category, Request $req, EntityManagerInterface $em,): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category updated successfully!');
            return $this->redirectToRoute('app_category_show');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/categories/delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $em,): Response
    {
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'Category deleted successfully!');
        return $this->redirectToRoute('app_category_show');
    }
}
