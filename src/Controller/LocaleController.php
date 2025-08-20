<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale(string $locale, Request $request, SessionInterface $session): RedirectResponse
    {
        // Vérifie qu'on a une locale autorisée
        if (!in_array($locale, ['fr', 'en'])) {
            $locale = 'fr';
        }

        // Stocke la locale dans la session
        $session->set('_locale', $locale);

        // Redirige vers la page précédente
        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('home'));
    }
}
