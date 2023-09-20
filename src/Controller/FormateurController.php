<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use App\Repository\FormateurRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $formateurRepository): Response
    {
        $formateurs = $formateurRepository->findAll();
        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs,
        ]);
    }

    // Méthode pour afficher le détail d'un formateur et les sessions associées
    #[Route('/formateur/{id}', name: 'show_formateur')]
    public function show(int $id, FormateurRepository $formateurRepository, SessionRepository $sessionRepository): Response
    {
        $formateur = $formateurRepository->find($id);
        $sessions = $sessionRepository->findSessionsByFormateur($id);
        return $this->render('formateur/show.html.twig', [
            'formateur' => $formateur,
            'sessions' => $sessions,
        ]);
    }
}
