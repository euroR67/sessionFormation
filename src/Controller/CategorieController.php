<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\ModulesRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Méthode pour ajouter une catégorie et pour modifier une catégorie
    #[Route('/categorie/add', name: 'new_categorie')]
    #[Route('/categorie/{id}/edit', name: 'edit_categorie')]
    public function new_edit(Categorie $categorie = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$categorie) {
            $categorie = new Categorie();
        }

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Message flash de succès pour l'ajout ou la modification d'une catégorie
            $this->addFlash('success', 'Catégorie ' . ($categorie->getId() ? 'modifiée' : 'ajoutée') . ' avec succès !');
            
            $categorie = $form->getData();

            $entityManager->persist($categorie);
            $entityManager->flush();


            return $this->redirectToRoute('app_categorie');

        }

        return $this->render('categorie/new.html.twig', [
            'form' => $form,
            'edit'=> $categorie->getId()
        ]);
    }

    // Méthode pour supprimer une catégorie
    #[Route('/categorie/{id}/delete', name: 'delete_categorie')]
    public function delete(Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($categorie);
        $entityManager->flush();

        // Message flash de succès pour la suppression d'une catégorie
        $this->addFlash('success', 'Catégorie supprimée avec succès !');

        return $this->redirectToRoute('app_categorie');
    }

    // Méthode pour afficher le détail d'une catégorie et les modules associés avec la méthode findModulesByCategorie
    #[Route('/categorie/{id}', name: 'show_categorie')]
    public function show($id, CategorieRepository $categorieRepository, ModulesRepository $modulesRepository): Response
    {

        // Utilisez directement l'ID de la catégorie sans ParamConverter
        $categorie = $categorieRepository->find($id);

        if(!$categorie){
            $this->addFlash('error', 'La catégorie demandée n\'existe pas !');
            return $this->redirectToRoute('app_categorie');
        }

        $categorie = $categorieRepository->find($id);
        $modules = $modulesRepository->findModulesByCategorie($id);
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
            'modules' => $modules,
        ]);
    }
    
}
