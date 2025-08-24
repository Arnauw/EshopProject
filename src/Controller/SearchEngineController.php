<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchEngineController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, ProductRepository $productRepo, PaginatorInterface $paginator): Response
    {

        $word = $request->query->get('word', '');

        if ($word !== '') {

            $results = $productRepo->searchEngine($word);

            $products = $paginator->paginate($results, $request->query->getInt('page', 1), 8);
        } else {
            $products = [];
        }

        return $this->render('search_engine/index.html.twig', [
            'products' => $products,
            'word' => $word,
        ]);
    }
}
