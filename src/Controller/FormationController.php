<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    // Méthode pour ajouter une formation et pour modifier une formation
    #[Route('/formation/add', name: 'new_formation')]
    #[Route('/formation/{id}/edit', name: 'edit_formation')]
    public function new_edit(Formation $formation = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$formation) {
            $formation = new Formation();
        }

        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $formation = $form->getData();

            $entityManager->persist($formation);
            $entityManager->flush();

            // Message flash de succès pour l'ajout ou la modification d'une formation
            $this->addFlash('success', 'Formation ' . ($formation->getId() ? 'modifiée' : 'ajoutée') . ' avec succès !');

            return $this->redirectToRoute('app_formation');

        }

        return $this->render('formation/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Méthode pour supprimer une formation
    #[Route('/formation/{id}/delete', name: 'delete_formation')]
    public function delete(Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($formation);
        $entityManager->flush();

        // Message flash de succès pour la suppression d'une formation
        $this->addFlash('success', 'Formation supprimée avec succès !');

        return $this->redirectToRoute('app_formation');
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
