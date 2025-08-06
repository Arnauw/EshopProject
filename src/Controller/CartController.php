<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{

    public function __construct(private readonly ProductRepository $productRepository,)
    {

    }

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function cart(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        $cartWithData = [];

//        dd($cart);

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity,
            ];
        }

        $total = array_sum(
            array_map(function ($item) {
                return $item['product']->getPrice() * $item['quantity'];
            }, $cartWithData)
        );

//        dd($total);
//        dd($cartWithData);

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData, // on retourne ces deux variables afin de les récupérer dans la vue
            'total' => $total,
        ]);
    }


    #[Route('/cart/add/{id}/', name: 'app_cart_add_product', methods: ['GET'])]
    public function addToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route("/cart/remove/{id}/", name: "app_cart_remove_product", methods: ['GET'])]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {
        // Récupération du contenu du panier en session, ou initialisation à un tableau vide si il n'existe pas
        $cart = $session->get('cart', []);
        // Vérification si le produit à supprimer existe dans le panier
        if (!empty($cart[$id])) {
            // Suppression du produit du panier
            unset($cart[$id]);
        }
        // Mise à jour du contenu du panier en session
        $session->set('cart', $cart);
        // Redirection vers la page du panier
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
