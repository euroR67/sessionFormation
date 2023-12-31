<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\SessionRepository;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    // Méthode pour ajouter un formateur et pour modifier un formateur
    #[Route('/formateur/add', name: 'new_formateur')]
    #[Route('/formateur/{id}/edit', name: 'edit_formateur')]
    public function new_edit(Formateur $formateur = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$formateur) {
            $formateur = new Formateur();
        }

        $form = $this->createForm(FormateurType::class, $formateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->addFlash('success', 'Formateur ' . ($formateur->getId() ? 'modifié' : 'ajouté') . ' avec succès !');
            
            $formateur = $form->getData();

            $entityManager->persist($formateur);
            $entityManager->flush();

            // Message flash de succès pour l'ajout ou la modification d'un formateur

            return $this->redirectToRoute('app_formateur');

        }

        return $this->render('formateur/new.html.twig', [
            'form' => $form,
            'edit'=> $formateur->getId()
        ]);
    }

    // Méthode pour supprimer un formateur
    #[Route('/formateur/{id}/delete', name: 'delete_formateur')]
    public function delete(Formateur $formateur, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($formateur);
        $entityManager->flush();

        // Message flash de succès pour la suppression d'un formateur
        $this->addFlash('success', 'Formateur supprimé avec succès !');

        return $this->redirectToRoute('app_formateur');
    }

    // Méthode pour afficher le détail d'un formateur et les sessions associées
    #[Route('/formateur/{id}', name: 'show_formateur')]
    public function show(int $id, FormateurRepository $formateurRepository, SessionRepository $sessionRepository): Response
    {

        // Utilisez directement l'ID du formateur sans ParamConverter
        $formateur = $formateurRepository->find($id);

        // à ce stade, $formateur contiendra soit une instance valide de formateur, soit null si l'ID n'existe pas
        if (!$formateur) {
            $this->addFlash('error', 'Cet page n\'existe pas !');
            return $this->redirectToRoute('app_formateur');
        }

        $formateur = $formateurRepository->find($id);
        $sessions = $sessionRepository->findSessionsByFormateur($id);
        return $this->render('formateur/show.html.twig', [
            'formateur' => $formateur,
            'sessions' => $sessions,
        ]);
    }
}
