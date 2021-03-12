<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    const ITEM_PER_PAGE = 3;

    /**
     * @Route("/", name="home")
     */
    public function index($page = 1,Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var ArticleRepository $articleRepository */
        $articleRepository = $entityManager->getRepository(Article::class);
        $articles = $articleRepository->findByMaxView(self::ITEM_PER_PAGE);
        return $this->render('home/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/about-us", name="about_us")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
        ]);
    }
}
