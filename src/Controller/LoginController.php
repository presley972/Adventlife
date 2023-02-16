<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils, ManagerRegistry $doctrine): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $urlLogin = $this->generateUrl('login',[], UrlGeneratorInterface::ABSOLUTE_URL);
        $urlRegister = $this->generateUrl('user_api',[], UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->render('user/mainLogin.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'last_username' => $lastUsername,
            'error'         => $error,
            'urlForm' => $urlLogin,
            'urlRegister' => $urlRegister
        ]);
      }
}
