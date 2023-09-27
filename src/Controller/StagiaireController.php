<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(StagiaireRepository $stagiaireRepository): Response
    {
        $stagiaires = $stagiaireRepository->findBy([], ['nom' => 'ASC']);
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    // Méthode pour ajouter un stagiaire et pour modifier un stagiaire
    #[Route('/stagiaire/add', name: 'new_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
    public function new_edit(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$stagiaire) {
            $stagiaire = new Stagiaire();
        }

        $form = $this->createForm(StagiaireType::class, $stagiaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Message flash de succès pour l'ajout ou la modification d'un stagiaire
            $this->addFlash('success', 'Stagiaire ' . ($stagiaire->getId() ? 'modifié' : 'ajouté') . ' avec succès !');

            $stagiaire = $form->getData();

            $entityManager->persist($stagiaire);
            $entityManager->flush();


            return $this->redirectToRoute('app_stagiaire');

        }

        return $this->render('stagiaire/new.html.twig', [
            'form' => $form,
            'edit'=> $stagiaire->getId()
        ]);
    }

    // Méthode pour supprimer un stagiaire
    #[Route('/stagiaire/{id}/delete', name: 'delete_stagiaire')]
    public function delete(Stagiaire $stagiaire, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($stagiaire);
        $entityManager->flush();

        // Message flash de succès pour la suppression d'un stagiaire
        $this->addFlash('success', 'Stagiaire supprimé avec succès !');

        return $this->redirectToRoute('app_stagiaire');
    }

    // Méthode pour afficher le détail d'un stagiaire
    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show($id, StagiaireRepository $stagiaireRepository): Response
    {

        $stagiaire = $stagiaireRepository->find($id);

        if (!$stagiaire) {
            $this->addFlash('error', 'Page demandé non trouvée !');
            return $this->redirectToRoute('app_stagiaire');
        }

        return $this->render('stagiaire/show.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }
}
