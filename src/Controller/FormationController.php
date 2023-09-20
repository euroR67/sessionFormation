<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    
    // Fonction pour afficher le détail d'une formation ainsi que les sessions associées avec la fonction findSessionsByFormation
    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(int $id, FormationRepository $formationRepository, SessionRepository $sessionRepository): Response
    {
        $formation = $formationRepository->find($id);
        $sessions = $sessionRepository->findSessionsByFormation($id);
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'sessions' => $sessions,
        ]);
    }

    
}
