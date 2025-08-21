<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchEngineController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['POST'])]
    public function index(Request $request, $productRepo): Response
    {



        $request->isMethod(Request::METHOD_POST);


        $search = $productRepo->searchEngine('juva');

        return $this->render('search_engine/index.html.twig', [
//            'products' => $results,
//            'word' => $word,
        ]);
    }
}
