<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/articles/add', name: 'add_article')]
    public function addArticle (ManagerRegistry $doctrine, Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_article', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/addArticle.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/articles/{slug}', name: 'show_article')]
    public function show(string $slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBySlug($slug);
        return $this->render('article/index.html.twig', [
            'article' => $article,
        ]);
    }



}
