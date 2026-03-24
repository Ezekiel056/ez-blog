<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\AddArticleType;
use App\Repository\ArticlesRepository;
use App\Repository\ThemesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/articles/{id}', name: 'app_articles_show')]
    public function show(Articles $article, ): Response
    {

        if ($article->getId() > 0) {
            $converter = new CommonMarkConverter();
            $article->setContent($converter->convert($article->getContent()));
           return  $this->render('articles/show.html.twig', [
                'article' => $article
            ]);
        }
        $this->addFlash('error','L\'article demandé n\'existe pas');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/articles/add', name: 'app_articles_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_REDACTOR')) {
            $this->addFlash('error','Vous n\'avez pas les droits nécéssaires pour effectuer cette action');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(AddArticleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleData = $form->getData();
            $article = new Articles();
            $article->setTitle($articleData['title']);
            $article->setContent($articleData['content']);
            $article->setResume($articleData['resume']);
            $article->setUser($this->getUser());

            /** @var UploadedFile|null $file */
            $file = $articleData['imageFile'];
            $uploadDir = $this->getParameter('kernel.project_dir')
                . '/assets/images/articles';

            $newFilename = uniqid() . '.webp';

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            $image->scale(width: 800);
            $image->toWebp(80)->save($uploadDir . '/' . $newFilename);

            $article->setImage($newFilename);

            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article ajouté avec succès!');
            return $this->redirectToRoute('app_home');

        }

        return $this->render('articles/add.html.twig', [
            'addArticleForm' => $form
        ]);
    }


}
