<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart {

    public function __construct(private RequestStack $requestStack)
    {

    }

    /**
     * add()
     * function permettant d'ajouter un produit à mon panier
     * @param mixed $product
     * @return void
     */
    public function add($product) {
        // Appeler la session de symfony
        $cart= $this->getCart();

        if(isset($cart[$product->getId()])) {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' =>  $cart[$product->getId()]['qty'] + 1
            ];
        }else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }
            
    

        // créer ma session Cart
        $this->requestStack->getSession()->set('cart', $cart);
    }

    /**
     * getCart()
     * function retournant le panier
     * @return mixed
     */
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');

    }

    /**
     * remove()
     * function permettant de supprimer tous les produits de mon panier
     * @return mixed
     */
    public function remove() {
        return $this->requestStack->getSession()->remove('cart');
    }

    /**
     * decrease()
     * function permettant de supprimer un produit dans mon panier
     * @param mixed $id
     * @return void
     */
    public function decrease($id)
    {
        $cart = $this->getCart();

        if($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] -1;
        }else {
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    /**
     * fullQuantity()
     * function pour retouner la quantité total des produits de mon panier
     * @return float|int
     */
    public function fullQuantity()
    {
        $cart = $this->getCart();
        $quantity = 0;

        if(!isset($cart)) {
            return $quantity;
        }

        foreach($cart as $product) {
            $quantity = $quantity + $product['qty'];
        }
        return $quantity;
    }

    /**
     * getTotalWt()
     * function permettant de retourner le prix total des produits de mon panier
     * @return float|int
     */
    public function getTotalWt() {
        $cart = $this->getCart();
        $price = 0;

        if(!isset($cart)){
            return $price;
        }

        foreach($cart as $product) {
            $price = $price +($product['object']->getPrice() * $product['qty']);
        }
        return $price;
    }
}