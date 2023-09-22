<?php

namespace App\Controller;

use App\Entity\Modules;
use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Repository\ModulesRepository;
use App\Repository\SessionRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findBy([], ['dateSession' => 'ASC']);
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    // Méthode pour ajouter une session et pour modifier une session
    #[Route('/session/add', name: 'new_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$session) {
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $session = $form->getData();

            // On ajoute la session à chaque stagiaire
            foreach ($session->getStagiaires() as $stagiaire) {
                $stagiaire->addSession($session);
                $entityManager->persist($stagiaire);
            }

            $entityManager->persist($session);
            $entityManager->flush();

             // Message flash de succès pour l'ajout ou la modification d'une session
            $this->addFlash('success', 'Session ' . ($session->getId() ? 'modifiée' : 'ajoutée') . ' avec succès !');

            return $this->redirectToRoute('app_session');
        }

        return $this->render('session/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Méthode pour supprimer une session
    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($session);
        $entityManager->flush();

        // Message flash de succès pour la suppression d'une session
        $this->addFlash('success', 'Session supprimée avec succès !');

        return $this->redirectToRoute('app_session');
    }

    // Méthode pour ajouter un stagiaire à une session
    #[Route('/session/addStagiaire/{id}/{stagiaire}', name: 'add_stagiaire')]
    public function addStagiaire(Session $session, Stagiaire $stagiaire, EntityManagerInterface $entityManager): Response
    {

        // Vérifier si la session a atteint sa capacité maximale
        if ($session->getCapacite() <= 0) {
            $this->addFlash('error', 'La session est complète');
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }


        // On ajoute le stagiaire à la session et la session au stagiaire
        $session->addStagiaire($stagiaire);
        // On ajoute la session au stagiaire
        $stagiaire->addSession($session);
        // On persiste les modifications
        $entityManager->persist($session);
        $entityManager->persist($stagiaire);
        // On enregistre les modifications
        $entityManager->flush();

        $this->addFlash('success', 'Stagiaire inscrit avec succès !');

        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }

    // Méthode pour retirer un stagiaire d'une session 
    #[Route('/session/removeStagiaire/{id}/{stagiaire}', name: 'remove_stagiaire')]
    public function removeStagiaire(Session $session, Stagiaire $stagiaire, EntityManagerInterface $entityManager): Response
    {
        // On retire le stagiaire de la session et la session du stagiaire
        $session->removeStagiaire($stagiaire);
        // On retire la session du stagiaire
        $stagiaire->removeSession($session);
        // On persiste les modifications
        $entityManager->persist($session);
        $entityManager->persist($stagiaire);
        // On enregistre les modifications
        $entityManager->flush();

        $this->addFlash('success', 'Stagiaire désinscrit avec succès !');

        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }

    // Méthode pour afficher le détail d'une session et les modules associés
    #[Route('/session/{id}', name: 'show_session')]
    public function show(SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository,Session $session, ModulesRepository $modulesRepository): Response
    {

        $stagiairesNonInscrit = $sessionRepository->findNonInscrits($session->getId());
        $modulesNonProgrammer = $sessionRepository->findNonProgrammer($session->getId());

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'stagiairesNonInscrit' => $stagiairesNonInscrit,
            'modulesNonProgrammer' => $modulesNonProgrammer,
            'stagiaires' => $stagiaireRepository->findAll(),
            'modules' => $modulesRepository->findAll()
        ]);
    }

    // Méthode pour retirer un programme d'une session
    #[Route('/session/removeProgramme/{id}/{programme}', name: 'remove_module')]
    public function removeProgramme(Session $session, Programme $programme, EntityManagerInterface $entityManager) : Response
    {
        // On retire le programme de la session et la session du programme
        $session->removeProgramme($programme);

        // On persiste les modifications
        $entityManager->persist($session);
        $entityManager->persist($programme);
        
        // On enregistre les modifications
        $entityManager->flush();

        // Message flash de succès
        $this->addFlash('success', 'Programme retiré avec succès !');

        // On redirige vers la page de la session
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }

    // Méthode pour ajouter un module à une session
    #[Route('/session/addModule/{id}/{module}', name: 'add_module')]
    public function addModule(Session $session, Modules $module, Request $request, EntityManagerInterface $entityManager): Response
    {
        // On récupère le nombre de jours pour le programme à ajouter
        $nbJours = $request->request->get('duree');

        // on dump/die $nbJours pour vérifier qu'il est bien récupéré
        // dd($nbJours);

        // Si le nombre de jours est bien saisi est n'est pas null
        if($nbJours != null) {

            $programme = new Programme();

            // On ajoute le programme à la session et la session au programme
            $programme->setSession($session);
            // On ajoute le module au programme et le programme au module
            $programme->setModule($module);
            // On ajoute le nombre de jours au programme
            $programme->setDureeJour($nbJours);

            // Persiste les modifications
            $entityManager->persist($programme);
            // Enregistre les modifications
            $entityManager->flush();

            // Message flash de succès
            $this->addFlash('success', 'Programme ajouté avec succès !');
        }

        // On redirige vers la page de la session
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }
    
}
