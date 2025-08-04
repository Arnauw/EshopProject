<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('homepage/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
}
