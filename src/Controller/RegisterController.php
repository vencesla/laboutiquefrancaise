<?php

namespace App\Controller;

use App\Form\RegisterUserType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {          
            $entityManager->persist($user);
            $entityManager->flush();


            $this->addFlash(
                'success',
                "Votre compte est correctement créé. Veillez vous connecter."
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
        'registerForm' => $form->createView()
        ]);
    }
}
