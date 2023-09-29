<?php

namespace App\Controller;

use TCPDF;
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

        // Récupérez le nombre actuel de stagiaires inscrits dans la session
        $currentNbStagiaires = $session->getNbStagiaires();

        $form = $this->createForm(SessionType::class, $session, [
            'current_nb_stagiaires' => $currentNbStagiaires, // Passez le nombre actuel de stagiaires inscrits comme option
        ]);

        $form->handleRequest($request);

        // Vérifiez si le formulaire a été soumis et si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Message flash de succès pour l'ajout ou la modification d'une session
            $this->addFlash('success', 'Session ' . ($session->getId() ? 'modifiée' : 'ajoutée') . ' avec succès !');
            // Récupérez les données du formulaire
            $sessionData = $form->getData();

            // Vérifiez si la session existe déjà
            if ($session->getId() !== null) {
                // Session existante, vérifiez si la nouvelle valeur de nbPlace est inférieure au nombre de stagiaires inscrits
                if ($sessionData->getNbPlace() < $session->getNbStagiaires()) {
                    // La nouvelle valeur de nbPlace est inférieure, affichez une erreur
                    $this->addFlash('error', 'Le nombre de place doit être supérieure ou égale à la valeur actuelle.');
                    return $this->redirectToRoute('edit_session', ['id' => $session->getId()]);
                }
            } // Si la session n'existe pas, vérifiez si le nombre de places n'est pas inférieur au nombre de stagiaires selectionnés
            elseif ($sessionData->getNbPlace() < $sessionData->getStagiaires()->count()) {
                // La nouvelle valeur de nbPlace est inférieure, affichez une erreur
                $this->addFlash('error', 'Le nombre de place doit être supérieure ou égale au nombre de stagiaires selectionnés.');
                return $this->redirectToRoute('new_session');
            }

            // On ajoute la session à chaque stagiaire
            foreach ($session->getStagiaires() as $stagiaire) {
                $stagiaire->addSession($session);
                $entityManager->persist($stagiaire);
            }

            // On persiste les modifications (prépare la requête)
            $entityManager->persist($session);
            // On enregistre les modifications(éxécute la requête)
            $entityManager->flush();


            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }

        return $this->render('session/new.html.twig', [
            'form' => $form,
            'edit'=> $session->getId()
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
    public function show($id,SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository, ModulesRepository $modulesRepository): Response
    {
        // Utilisez directement l'ID de la session sans ParamConverter
        $session = $sessionRepository->find($id);

         // À ce stade, $session contiendra soit une instance valide de Session, soit null si l'ID n'existe pas.
        if (!$session) {
            // Gérer le cas où l'ID de la session n'existe pas, rediriger vers la liste des sessions avec un message flash d'erreur
            $this->addFlash('error', 'La session demandée n\'existe pas');
            return $this->redirectToRoute('app_session');
        }

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

    #[Route('/session/{id}/generate-pdf/{stagiaireId}', name: 'generate_pdf_attestation')]
    public function generatePdf($id, $stagiaireId, SessionRepository $sessionRepository, StagiaireRepository $stagiaireRepository): Response
    {
        // Récupérez la session
        $session = $sessionRepository->find($id);
    
        // Récupérez le stagiaire en fonction de l'identifiant
        $stagiaire = $stagiaireRepository->find($stagiaireId);
    
        // Vérifiez si la session et le stagiaire existent
        if (!$session || !$stagiaire) {
            $this->addFlash('error', 'La session ou le stagiaire demandé n\'existe pas');
            return $this->redirectToRoute('app_session');
        }
    
        // Créez un nouvel objet TCPDF
        $pdf = new TCPDF();
        $pdf->SetMargins(10, 10, 10); // Définissez les marges du document
        $pdf->AddPage();
    
        // Titre de l'attestation
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Attestation d\'entrée en formation', 0, 1, 'C');
    
        // Ligne de séparation
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->SetLineWidth(0.2);
    
        // Informations sur la session
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 10, 'Nom de la session : ' . $session->getNomSession(), 0, 1);
        $pdf->Cell(0, 10, 'Formateur : ' . $session->getFormateur(), 0, 1); // Utilisation de __toString
        $pdf->Cell(0, 10, 'Date de début : ' . $session->getDateSession()->format('d/m/Y'), 0, 1);
    
        $endDate = clone $session->getDateSession();
        $endDate->modify('+' . $session->getDureeJours() . ' days');
        $pdf->Cell(0, 10, 'Date de fin : ' . $endDate->format('d/m/Y'), 0, 1);
    
        // Texte de l'attestation
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->MultiCell(0, 10, "Ce document atteste que $stagiaire est inscrit(e) à la session de formation \"{$session->getNomSession()}\" de l'organisme Elan Formation. La formation a débuté le {$session->getDateSession()->format('d/m/Y')} et se terminera le {$endDate->format('d/m/Y')}.", 0, 'L');
    
        // Signature
        $pdf->SetY($pdf->GetY() + 10); // Espacement
        $pdf->Cell(0, 10, 'Fait à Strasbourg, le ' . date('d/m/Y'), 0, 1, 'R');
    
        // Nom du signataire
        $pdf->SetY($pdf->GetY() + 10); // Espacement
        $pdf->Cell(0, 10, 'Signature : ________________________________', 0, 1, 'R');
    
        // Nom et titre du signataire
        $pdf->Cell(0, 10, 'Nom et titre du signataire : ________________________________', 0, 1, 'R');
    
        // Renvoyez le PDF en réponse HTTP pour le téléchargement
        $response = new Response($pdf->Output('attestation_formation.pdf', 'I'));
        $response->headers->set('Content-Type', 'application/pdf');
    
        return $response;
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
