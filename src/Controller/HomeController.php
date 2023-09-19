<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $currentSessions = $sessionRepository->findCurrentSessions();
        $futureSessions = $sessionRepository->findFutureSessions();
        $pastSessions = $sessionRepository->findPastSessions();
        
        return $this->render('home/index.html.twig', [
            'currentSessions' => $currentSessions,
            'futureSessions' => $futureSessions,
            'pastSessions' => $pastSessions,
        ]);
    }
}
