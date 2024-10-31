<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
        ]);
    }

    #[Route('/mon-panier/add/{id}', name: 'app_add_cart')]
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request)
    {
        $product = $productRepository->findOneById($id);

        $cart->add($product);

        $this->addFlash(
            'success',
            "Produit correctement ajoué à votre panier."
        );

        return $this->redirect($request->headers->get(('referer')));
    }

    #[Route('/cart/decrease/{id}', name: 'app_decrease_cart')]
    public function decrease($id, Cart $cart)
    {
        $cart->decrease($id);

        $this->addFlash(
            'success',
            "Produit suppimé de votre panier."
        );

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove', name: 'app_remove_cart')]
    public function remove(Cart $cart)
    {
        $cart->remove();

        $this->addFlash(
            'success',
            "Votre panier a été correctement supprimé."
        );

        return $this->redirectToRoute('app_home');
    }
}
