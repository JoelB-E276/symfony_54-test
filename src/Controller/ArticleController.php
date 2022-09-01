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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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


    #[Route('/articles/add', name: 'add_article', methods: ['GET', 'POST'])]
    public function addArticle (ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
                $article->setPhoto($newFilename);

                try {
                    $photoFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
            }    
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


    /**
     * @method findOneBySlug() Custom query to get Article by @param $slug
     */
    #[Route('/article/{slug}', name: 'show_article')]
    public function show(string $slug, int $id = \null, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBySlug($slug);
        $allArticles = $articleRepository->findAll();

        return $this->render('article/show.html.twig', [
            'article' => $article ,
            'allArticles' => $allArticles

        ]);
    }


    /**
     * @method findOneBySlug() Custom query to get Article by @param $slug 
     */
    #[Route('/article/update/{slug}', name: 'edit_article', methods: ['GET', 'POST'])]
    public function edit(string $slug, int $id = \null, ArticleRepository $articleRepository, ManagerRegistry $doctrine, Request $request): Response
    {
        $article = $articleRepository->findOneBySlug($slug);
        $dataArticle = new Article();
        $dataArticle->setTitle($article->getTitle());
        $dataArticle->setSlug($article->getSlug());

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()) 
        {
            /**
             * $article is hydrated by the data ($dataArticle) retrieved from 
             * the entity because class ArticleType removes it when sending the form
             */
            $article->setTitle($dataArticle->getTitle());
            $article->setSlug($dataArticle->getSlug());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView() 
        ]);
    }


    #[Route('/article/delete/{slug}', name: 'delete_article', methods: ['GET', 'POST'])]
    public function delete(string $slug, ArticleRepository $articleRepository, ManagerRegistry $doctrine, Request $request): Response
    {
        $article = $articleRepository->findOneBySlug($slug);
        
        if ($this->isCsrfTokenValid('delete'.$article->getSlug(), $request->request->get('token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article');       
        }
        
        return $this->render('article/delete.html.twig', [
            'article' => $article ,
        ]);

    }


}    
