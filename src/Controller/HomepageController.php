<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(ProductRepository $productRepository,
                          CategoryRepository $categoryRepository,
                          Request $request,
                          PaginatorInterface $paginator): Response
    {
        $data = $productRepository->findby([], ['id' => "DESC"]);

        $products = $paginator->paginate($data, $request->query->getInt('page', 1), 8,);

        return $this->render('homepage/index.html.twig', [
            'products' => $products,
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
    public function filter(SubCategory $subCategory, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $data = $subCategory->getProducts();

        $products = $paginator->paginate($data, $request->query->getInt('page', 1), 8);

        return $this->render('homepage/filter.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
            'subcategory' => $subCategory,
        ]);
    }

}
