<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProduct;
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
    #[Route(name: 'app_order', methods: ['GET', 'POST'])]
    public function index(
        Request                $request,
        SessionInterface       $session,
        EntityManagerInterface $entityManager,
        Cart                   $cart
    ): Response
    {
        $data = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!empty($data['total'])) {
                $totalPrice = $data['total'] + $order->getCity()->getShippingCost();

                $order->setTotalPrice($totalPrice);
                $order->setCreatedAt(new \DateTimeImmutable());
//                $order->setIsPaymentCompleted(0);
                //dd($order);
                $entityManager->persist($order);
                $entityManager->flush();

                foreach ($data['cart'] as $value) {

                    $orderProduct = new OrderProduct();
                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($value['product']);
                    $orderProduct->setQuantity($value['quantity']);

                    $entityManager->persist($orderProduct);
                    $entityManager->flush();
                }

                if ($order->isPayingOnDelivery()) {

                    $session->set('cart', []);

                    return $this->render('order/order_message.html.twig', [
                        'order' => $order
                    ]);

//                    $html = $this->renderView('mail/orderConfirm.html.twig',[
//                        'order'=>$order
//                    ]);

//                    $email = (new Email())
//                    ->from('sneakhub@gmailcom')
//                    //->to('to@gmailcom')
//                    ->to($order->getEmail())
//                        ->subject('Confirmation de réception de commande')
//                        ->html($html);
//                    $this->mailer->send($email);

//                    return $this->redirectToRoute('app_order_message');
                }

//                $paymentStripe = new StripePayment(); //on importe notre service avec sa classe
//                $shippingCost = $order->getCity()->getShippingCost();
//                $paymentStripe->startPayment($data, $shippingCost, $order->getId()); //on importe le panier donc $data
//                $stripeRedirectUrl = $paymentStripe->getStripeRedirectUrl();
//                //dd( $stripeRedirectUrl);
//                return $this->redirect($stripeRedirectUrl);
            }
        }

//        return $this->render('order/index.html.twig', [
//            'form'=>$form->createView(),
//            'total'=>$data['total'],
//        ]);

        return $this->render('order/index.html.twig', [
            'orders' => $order,
            'form' => $form,
            'total' => $data['total'],
        ]);

//        OLD VERSION BEFORE CART SERVICE
//        $cart = $session->get('cart', []);
//
//        $order = new Order();
//        $form = $this->createForm(OrderType::class, $order);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($order);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_order', [], Response::HTTP_SEE_OTHER);
//        }
//
//
//        $cartWithData = [];
//
////        dd($cart);
//
//        foreach ($cart as $id => $quantity) {
//            $cartWithData[] = [
//                'product' => $productRepository->find($id),
//                'quantity' => $quantity,
//            ];
//        }
//
//        $total = array_sum(
//            array_map(function ($item) {
//                return $item['product']->getPrice() * $item['quantity'];
//            }, $cartWithData)
//        );
//
//
//        return $this->render('order/index.html.twig', [
//            'orders' => $order,
//            'form' => $form,
//            'total' => $total,
//        ]);


    }

    #[Route('/order_message', name: 'app_order_message')]
    public function orderMessage(): Response
    {
        return $this->render('order/order_message.html.twig');
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

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status' => 200, "message" => 'on', 'content' => $cityShippingPrice]));
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
