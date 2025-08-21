<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\User;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use App\Service\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{

    #[Route('/order', name: 'app_order', methods: ['GET', 'POST'])]
    public function index(
        Request                $request,
        SessionInterface       $session,
        EntityManagerInterface $entityManager,
        Cart                   $cart,
        MailerInterface        $mailer,
    ): Response
    {
        $data = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            if ($order->isPayingOnDelivery()) {
//            }
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

                $session->set('cart', []);

//dd($orderProduct);

                // MAILTRAP IS BLOCKED BY DSI, FUCK THEM
//                $html = $this->renderView('mail/orderConfirm.html.twig', [
//                    'order' => $order,
//                    'orderProducts' => $data['cart'],
//                ]);
//                $email = new Email()
//                    ->from('amineshop@test.com')
//                    ->to('arnaud.rabel@gmail.com')
////                    ->to($order->getEmail())
//                    ->subject('Order Confirmation')
//                    ->html($html);
//                $mailer->send($email);


                $paymentStripe = new StripePayment();

                $shippingCost = $order->getCity()->getShippingCost();
                $paymentStripe->startPayment($data, $shippingCost, $order->getId());
                $stripeRedirectUrl = $paymentStripe->getStripeRedirectUrl();
                //dd( $stripeRedirectUrl);
                return $this->redirect($stripeRedirectUrl);

//                return $this->render('mail/orderConfirm.html.twig', [
//                    'order' => $order,
//                    'orderProducts' => $data['cart'],
//                ]);

            }

        }

        return $this->render('order/index.html.twig', [
            'orders' => $order,
            'form' => $form,
            'total' => $data['total'],
        ]);

    }

    #[Route('/order/order_message', name: 'app_order_message')]
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

        return $this->render('orders.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/order/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status' => 200, "message" => 'on', 'content' => $cityShippingPrice]));
    }

    #[Route('admin/order/orders/{type}', name: 'app_order_list', methods: ['GET'])]
    public function orderList($type, OrderRepository $orderRepo, PaginatorInterface $paginator, Request $request): Response
    {

        if ($type === 'is-completed') {
            $data = $orderRepo->findBy(['isCompleted' => 1], ['id' => 'DESC']);
        } else if ($type === 'pay-on-stripe-not-delivered') {
            $data = $orderRepo->findBy(['isCompleted' => 0, 'isPaymentCompleted'=>1,
//                'payOnDelivery'=>0,
            ],
                ['id' => 'DESC']);
        } else if ($type === 'pay-on-stripe-is-delivered') {
            $data = $orderRepo->findBy(['isCompleted' => 1, 'isPaymentCompleted'=>1,
//                'payOnDelivery'=>0,
            ],
                ['id' => 'DESC']);
        } else if ($type === 'no_delivery') {
            $data = $orderRepo->findBy(['isCompleted' => 0, 'isPaymentCompleted'=>0,
//                'payOnDelivery'=>0,
            ],
                ['id' => 'DESC']);
        } else if ($type === 'all') {
            $data = $orderRepo->findBy([], ['id' => "DESC"]);
        }

        $orders = $paginator->paginate($data, $request->query->getInt('page', 1), 1);

        return $this->render('order/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/order_message.html.twig', [
            'order' => $order,
        ]);
    }

//    #[Route('/order/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
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

    #[Route('/admin/order/{id}/delete', name: 'app_order_delete')]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        // Use parse_url to break the URL into its components.
        $urlParts = parse_url($request->headers->get('referer'));

        // Strips off the entire query string (?page=2, etc.).
        $pathWithoutQuery = $urlParts['path'];
        // Get the type from the referer header
        $type = basename($pathWithoutQuery);
//        dd($type);

        $entityManager->remove($order);
        $entityManager->flush();
        $this->addFlash('success', 'Deletion successful');



        return $this->redirectToRoute('app_order_list', ['type' => $type], Response::HTTP_SEE_OTHER);
    }

    #[Route('/editor/order/{id}/is-completed/update', name: 'app_orders_is-completed-update')]
    public function isCompletedUpdate(Request $request, $id, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $order = $orderRepository->find($id);
        $order->setIsCompleted(true);
        $entityManager->flush();

        $this->addFlash('success', 'Update successful');

        return $this->redirect($request->headers->get('referer'));
        // return the previous route
    }


}
