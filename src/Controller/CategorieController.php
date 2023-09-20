<?php

namespace App\Controller;

use App\Repository\ModulesRepository;
use App\Repository\CategorieRepository;
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

    // Méthode pour afficher le détail d'une catégorie et les modules associés avec la méthode findModulesByCategorie
    #[Route('/categorie/{id}', name: 'show_categorie')]
    public function show($id, CategorieRepository $categorieRepository, ModulesRepository $modulesRepository): Response
    {
        $categorie = $categorieRepository->find($id);
        $modules = $modulesRepository->findModulesByCategorie($id);
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
            'modules' => $modules,
        ]);
    }
    
}
