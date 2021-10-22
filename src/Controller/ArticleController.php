<?php

namespace App\Controller;

use App\Classe\Search;
use App\Form\AddCartType;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use App\Repository\NewCollectionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        $articles = $articleRepository->findBy([], ['categorie' => "DESC"]);

        if ($form->isSubmitted() && $form->isValid()) {
            $articles = $articleRepository->findBySearch($search);
            if (count($articles) == 0 ) {
                $articles = $articleRepository->findAll();
            }
        }

        $articles_paginate = $paginator->paginate($articles, $request->query->getInt('page', 1), 8);
        return $this->render('article/index.html.twig', [
            'title' => 'Nos articles',
            'articles' => $articles_paginate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article-slug")
     */
    public function articleSlug(ArticleRepository $articleRepository, $slug, Request $request): Response
    {
        $article = $articleRepository->findOneBy(['slug' => $slug]);
        if ($article) {
            $comments = [];
            $other_articles = $articleRepository->findButOne($article->getCategorie(), $slug);

            $form = $this->createForm(AddCartType::class, null);

            return $this->render('article/article.html.twig', [
                'title' => $article->getName(),
                'article' => $article,
                'comments' => $comments,
                'other_articles' => $other_articles,
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/articles/{slug}", name="articles-by-categorie")
     */
    public function articleCategorie(Request $request, ArticleRepository $articleRepository, $slug, CategorieRepository $categorieRepository, PaginatorInterface $paginator): Response
    {
        $categorie = $categorieRepository->findOneBy(['slug' => $slug]);
        $articles = $articleRepository->findBy(['categorie' => $categorie]);
        if ($articles) {
            $comments = [];
            $other_articles = $articleRepository->findButOne($categorie, $slug);

            $articles_paginate = $paginator->paginate($articles, $request->query->getInt('page', 1), 8);
            return $this->render('article/index.html.twig', [
                'title' => "Nos $categorie",
                'articles' => $articles_paginate,
                'comments' => $comments,
                'other_articles' => $other_articles,
                'form' => "",
            ]);
        }
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/articles/collection/{slug}", name="articles-by-collection")
     */
    public function articlesCollection(Request $request, PaginatorInterface $paginator, ArticleRepository $articleRepository, $slug, NewCollectionRepository $collectionRepository): Response
    {
        $collection = $collectionRepository->findOneBy(['slug' => $slug]);
        $articles = $articleRepository->findBy(['newCollection' => $collection]);
        if ($articles) {
            $comments = [];

            $articles_paginate = $paginator->paginate($articles, $request->query->getInt('page', 1), 8);
            return $this->render('article/index.html.twig', [
                'title' => "Nos articles de la collection $collection",
                'articles' => $articles_paginate,
                'comments' => $comments,
                'form' => "",
            ]);
        }
        return $this->redirectToRoute('home');
    }
}
