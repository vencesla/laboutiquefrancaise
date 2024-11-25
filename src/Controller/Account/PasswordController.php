<?php 

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PasswordUserType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;


class PasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_password')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
       
        $user = $this->getUser();

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) { 
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                "Votre mot de passe est correctement mis à jour."
            );
            
        }

        return $this->render('account/password/index.html.twig',[
            'modifyFormPwd' => $form
        ]);
    }
}
