<?php


namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class Cart
{

    private RequestStack $requestStack;
    public function __construct(RequestStack $requestStack
    )
    {
        $this->requestStack = $requestStack;
    }

    public function getCart(ProductRepository $productRepository): array
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        return [
            'cart' => $cartWithData,
            'total' => $total
        ];
    }


    /**
     * Calculates the total number of items in the cart.
     */
    public function getCartQuantity(): int
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        // array_sum calculates the total of all quantities in the cart array
        return array_sum($cart);
    }
}
