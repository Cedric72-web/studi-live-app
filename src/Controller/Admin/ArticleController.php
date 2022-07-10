<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @Route("/admin/articles", name="articles_list")
     */
    public function articlesList(ArticleRepository $articleRepository)
    {
        // afficher la liste de tous les articles en bdd
        // utiliser la classe ArticleRepository et la méthode findAll()
        $articles = $articleRepository->findAll();

        // Affiche tous les articles dans un fichier twig
        return $this->render('admin/article/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    // supprimer un article
    /**
     * @Route("/admin/article/{id}/delete", name="article_delete")
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function articleDelete($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager)
    {
        // récupérer l'article ciblé par l'id dans l'url en bdd
        $article = $articleRepository->find($id);

        // le supprimer
        $entityManager->remove($article);
        $entityManager->flush();

        // rediriger vers la page de liste des articles
        return $this->redirectToRoute("articles_list");
    }

    // créer un article
    /**
     * @Route("/admin/article/create", name="article_create")
     */
    public function articleCreate(\Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $entityManager)
    {
        // créer un formulaire contenant tous les champs de ma classe articles
        $article = new Article();
        $articleForm = $this->createForm(ArticleType::class, $article);

        $articleForm->handleRequest($request);

        // utiliser les données envoyées par le formulaire pour créer un article en bdd
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
        }

        return $this->render('/admin/article/article_create.html.twig', [
            'articleForm' => $articleForm->createView()
        ]);
    }

    // modifier un article
    /**
     * @Route("/admin/article/{id}/update", name="article_update")
     */
    public function updateArticle($id, ArticleRepository $articleRepository, \Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $entityManager)
    {
        $article = $articleRepository->find($id);

        $articleForm = $this->createForm(ArticleType::class, $article);

        $articleForm->handleRequest($request);

        // utiliser les données envoyées par le formulaire pour créer un article en bdd
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
        }

        return $this->render('/admin/article/article_create.html.twig', [
            'articleForm' => $articleForm->createView()
        ]);
    }
}