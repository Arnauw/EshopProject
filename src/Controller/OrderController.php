<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route(name: 'app_order', methods: ['GET'])]
    public function index(
        OrderRepository        $orderRepository,
        ProductRepository      $productRepository,
        Request                $request,
        SessionInterface       $session,
        EntityManagerInterface $entityManager,
//        Cart $cart
    ): Response
    {

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order', [], Response::HTTP_SEE_OTHER);
        }

        $cart = $session->get('cart', []);

        $cartWithData = [];

//        dd($cart);

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity,
            ];
        }

        $total = array_sum(
            array_map(function ($item) {
                return $item['product']->getPrice() * $item['quantity'];
            }, $cartWithData)
        );


        return $this->render('order/index.html.twig', [
            'orders' => $order,
            'form' => $form,
            'total' => $total,
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/order.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

//    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
//    public function show(Order $order): Response
//    {
//        return $this->render('order/order_message.html.twig', [
//            'order' => $order,
//        ]);
//    }

//    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(OrderType::class, $order);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('order/edit.html.twig', [
//            'order' => $order,
//            'form' => $form,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
//    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($order);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
//    }

}
