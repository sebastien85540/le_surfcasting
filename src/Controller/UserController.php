<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

// use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/profile", name="profile_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
        ]);
    }

    /**
     * @Route("/delete", name="delete")
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(EntityManagerInterface $entityManager,Request $request,TokenStorageInterface $tokenStorage){
        $user = $this->getUser();
        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('home');

    }
}
