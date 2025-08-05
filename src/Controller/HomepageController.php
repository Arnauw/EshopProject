<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {

        return $this->render('homepage/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}/show', name: 'app_product_show', methods: ['GET'])]
    public function show($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        $lastProductsAdded = $productRepository->findBy([], ['id' => 'DESC'], 4);

        return $this->render('homepage/show.html.twig', [
            'product' => $product,
            'lastProducts' => $lastProductsAdded,
        ]);
    }

    #[Route('/product/subcategory/{id}/filter ', name: 'app_home_product_filter', methods: ['GET'])]
    public function filter(SubCategory $subCategory, CategoryRepository $categoryRepository): Response
    {
        $products = $subCategory->getProducts();

        return $this->render('homepage/filter.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}/addToCart', name: 'app_product_addToCart', methods: ['GET'])]
    public function addToCart(Product $product, ProductRepository $productRepository): Response
    {

        $lastProductsAdded = $productRepository->findBy([], ['id' => 'DESC'], 5);



        return $this->render('homepage/show.html.twig', [
            'product' => $product,
        ]);
    }
}
