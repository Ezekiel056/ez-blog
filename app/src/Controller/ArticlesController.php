<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use App\Repository\ThemesRepository;
use ContainerOxEuhxC\getThemesRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticlesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticlesRepository $articlesRepo, ThemesRepository $themesRepo): Response
    {
        $themes = $themesRepo->findAllUsedByArticles();;
        $articles = $articlesRepo->findBy([], ['created_at' => 'DESC']);
        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
            'themes' => $themes
        ]);
    }
}
