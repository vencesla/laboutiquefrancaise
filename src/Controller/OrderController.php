<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\Product;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    /**
     * 1ère étape du tunel d'achat
     * choix de l'adresse de livraison et du transporteur
     *
     * @return Response
     */
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        $address = $this->getUser()->getAddresses();
        if(count($address) == 0){
            return $this->redirectToRoute('app_account_addresses');
        }

        $form = $this->createForm(OrderType::class, null , [
            'addresses' =>$address,
            'action' => $this->generateUrl('app_order_summary')
        ]);

        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }

    /**
     * 2ère étape du tunel d'achat
     * Récap de la commande de l'utilisateur
     * Insertion dans la base de donnée
     * Préparation du paiement vers Stripe
     * choix de l'adresse de livraison et du transporteur
     *
     * @return Response
     */
    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {

        $products = $cart->getCart();

        if($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }
        $form = $this->createForm(OrderType::class, null, [
            'addresses' =>$this->getUser()->getAddresses()
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // stocker les informations en BD

            // Création de la chaine adresse
            $addressObj = $form->get('addresses')->getData();

            $address = $addressObj->getFirstname().' '.$addressObj->getLastname().'<br/>';
            $address .= $addressObj->getAddress().'<br>';
            $address .= $addressObj->getPostal(). ' ' .$addressObj->getCity().'<br/>';
            $address .= $addressObj->getPhone().'<br/>';
            $address .= $addressObj->getCountry();

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivry($address);

            foreach($products as $product) {
                $orderDetails = new OrderDetail();
                $orderDetails->setProductName($product['object']->getName());
                $orderDetails->setProductIllustration($product['object']->getIllustration());
                $orderDetails->setProductPrice($product['object']->getPrice());
                $orderDetails->setProductTva($product['object']->getTva());
                $orderDetails->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetails);
            }

            $entityManager->persist($order);
            $entityManager->flush();
        }


        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $cart,
            'totalWt' => $cart->getTotalWt(),
        ]);
    }
}
