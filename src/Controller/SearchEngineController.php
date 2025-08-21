<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchEngineController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['POST'])]
    public function search(Request $request, ProductRepository $productRepo): Response
    {

//        dd($request->isMethod(Request::METHOD_POST));

        if ($request->isMethod(Request::METHOD_POST)) {
            $word = $request->request->get('word');
            $results = $productRepo->searchEngine($word);
        } else {
            $word = '';
            $results = [];
        }

        return $this->render('search_engine/index.html.twig', [
            'products' => $results,
            'word' => $word,
        ]);
    }
}
