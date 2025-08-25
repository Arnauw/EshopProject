<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function cart(ProductRepository $productRepo, Cart $cart): Response
    {
        $data = $cart->getCart($productRepo);

        return $this->render('cart/index.html.twig', [
            'items'=>$data['cart'],
            'total'=>$data['total']
        ]);
    }

    #[Route('/cart/add/{id}/', name: 'app_cart_add_product', methods: ['GET'])]
    public function addToCart(Product $product, SessionInterface $session, Request $request): Response
    {
        $cart = $session->get('cart', []);

        $id = $product->getId();

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $quantityInCart = $cart[$id] ?? 0;

        if ($quantityInCart > $product->getStock()) {
            $this->addFlash('danger', 'You cannot add more of "' . $product->getName() . '" as it is out of stock.');

            return $this->redirect($request->headers->get('referer'));
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease_quantity', methods: ['GET'])]
    public function decreaseQuantity(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id]) && $cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route("/cart/remove/{id}/", name: "app_cart_remove_product", methods: ['GET'])]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {

        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {

            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route("/cart/remove", name: "app_cart_remove", methods: ['GET'])]
    public function remove(SessionInterface $session): Response
    {
        $session->remove('cart');
        // Redirection vers la page du panier
        return $this->redirectToRoute('app_cart');
    }
}
