<?php

namespace App\Controller;

use App\Entity\ProductHistory;
use App\Form\ProductHistoryType;
use App\Repository\ProductHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product/history')]
final class ProductHistoryController extends AbstractController
{
    #[Route(name: 'app_product_history_index', methods: ['GET'])]
    public function index(ProductHistoryRepository $productHistoryRepository): Response
    {
        return $this->render('product_history/index.html.twig', [
            'product_histories' => $productHistoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $productHistory = new ProductHistory();
        $form = $this->createForm(ProductHistoryType::class, $productHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($productHistory);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_history/new.html.twig', [
            'product_history' => $productHistory,
            'form' => $form,
        ]);
    }

//    #[Route('/{id}', name: 'app_product_history_show', methods: ['GET'])]
//    public function show(ProductHistory $productHistory): Response
//    {
//        return $this->render('product_history/order_message.html.twig', [
//            'product_history' => $productHistory,
//        ]);
//    }

    #[Route('/{id}/edit', name: 'app_product_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductHistory $productHistory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductHistoryType::class, $productHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product_history/edit.html.twig', [
            'product_history' => $productHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_history_delete', methods: ['POST'])]
    public function delete(Request $request, ProductHistory $productHistory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productHistory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($productHistory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_history_index', [], Response::HTTP_SEE_OTHER);
    }
}
