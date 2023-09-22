<?php

namespace App\Controller;

use App\Entity\Modules;
use App\Form\ModulesType;
use App\Repository\ModulesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModulesController extends AbstractController
{
    #[Route('/modules', name: 'app_modules')]
    public function index(ModulesRepository $modulesRepository): Response
    {
        $modules = $modulesRepository->findAll();
        return $this->render('modules/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    // Méthode pour ajouter un module et pour modifier un module
    #[Route('/modules/add', name: 'new_modules')]
    #[Route('/modules/{id}/edit', name: 'edit_modules')]
    public function new_edit(Modules $modules = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$modules) {
            $modules = new Modules();
        }

        $form = $this->createForm(ModulesType::class, $modules);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $modules = $form->getData();

            $entityManager->persist($modules);
            $entityManager->flush();

            // Message flash de succès pour l'ajout ou la modification d'un module
            $this->addFlash('success', 'Module ' . ($modules->getId() ? 'modifié' : 'ajouté') . ' avec succès !');

            return $this->redirectToRoute('app_modules');

        }

        return $this->render('modules/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Méthode pour supprimer un module
    #[Route('/modules/{id}/delete', name: 'delete_modules')]
    public function delete(Modules $modules, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($modules);
        $entityManager->flush();
        
        // Message flash de succès pour la suppression d'un module
        $this->addFlash('success', 'Module supprimé avec succès !');

        return $this->redirectToRoute('app_modules');
    }
}
