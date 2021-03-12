<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comments;
use App\Form\ArticleType;
use App\Form\CommentsType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article", name="article_")
 * Class ArticleController
 * @package App\Controller
 */
class ArticleController extends AbstractController
{

    const ITEM_PER_PAGE = 10;

    /**
     * @Route("/", name="list")
     * renvoie tous les articles dans un tableau
     */
    public function index($page = 1, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var ArticleRepository $articleRepository */
        $articleRepository = $entityManager->getRepository(Article::class);
        $articlePaginator = $articleRepository->findPage($page, self::ITEM_PER_PAGE);
        $nbTotalItem = count($articlePaginator);
        $nbPages = ceil($nbTotalItem / self::ITEM_PER_PAGE);

        return $this->render('article/index.html.twig', [
            'articles' => $articlePaginator,
            'page' => $page,
            'nbPage' => $nbPages,
        ]);
    }

    /**
     * @Route("/{id}", name="item")
     */
    public function item($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        /*-------------------------------  ARTICLES  ----------------------------------------------------*/
        $articleRepository = $entityManager->getRepository(Article::class);
        $article = $articleRepository->find($id);

        /*--------------------------------------------  FORMULAIRE  -------------------------------------------*/
        $comment = new Comments();
        $comment->setDate(new \DateTime('now'));
        $commentForm = $this->createForm(CommentsType::class, $comment);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setUserComment($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Le commentaire ' . $comment->getComment() . ' a bien été envoyé');
            return $this->redirectToRoute('article_item', [
                'id' => $id,
            ]);
        }

        return $this->render('article/item.html.twig',
            [
                'article' => $article,
                'commentForm' => $commentForm->createView(),
            ]
        );
    }

    /**
     * @Route("/new", name="new", priority=1000)
     */
    public function newArticle(EntityManagerInterface $entityManager,
                               Request $request): Response
    {

        $article = new Article();
        $articleForm = $this->createForm(ArticleType::class, $article);

        $articleForm->handleRequest($request);
        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $article->setAuthor($this->getUser());
            $entityManager->persist($article);
            $entityManager->flush();
            $this->addFlash('success', 'L\'article ' . $article->getTitle() . ' a bien été enregistré.');

            return $this->redirectToRoute('article_list');
        }
        return $this->render('article/new.html.twig',
            [
                'articleFormView' => $articleForm->createView(),
            ]
        );
    }
}
