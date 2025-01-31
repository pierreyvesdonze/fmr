<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/utilisateur/espace/perso', name: 'user_settings_index')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('user/index.html.twig', [
            
        ]);
    }
}
