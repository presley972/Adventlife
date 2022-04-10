<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $url = $this->generateUrl('user_api',[], UrlGeneratorInterface::ABSOLUTE_URL);
        $urlLogin = $this->generateUrl('login',[], UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'urlFormRegister' => $url,
            'urlFormLogin' => $urlLogin
        ]);
    }

    #[Route('/user/login', name: 'userLogin')]
    public function userLogin(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $urlLogin = $this->generateUrl('login',[], UrlGeneratorInterface::ABSOLUTE_URL);
        $urlRegister = $this->generateUrl('user_api',[], UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->render('user/testLogin.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'urlForm' => $urlLogin,
            'urlRegister' => $urlRegister
        ]);
    }

    #[Route('/api/user', name: 'user_api')]
    public function registerTest(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Request $request): Response
    {
        //dump($request->request);die();

        //$dataUser = json_decode($request->getContent(),true);


        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            // '$json' represents payload data sent by React/Angular/Vue
            // the merge of parameters is needed to submit all form fields
//            $form->submit(array_merge($dataUser, $request->request->all()));
            //$form->submit($request->request->all());

            if ($form->isSubmitted() ) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                //dump($user);die();
                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
//                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
//                    (new TemplatedEmail())
//                        ->from(new Address('presleylupon@gmail.com', 'Adventlife Mail Bot'))
//                        ->to($user->getEmail())
//                        ->subject('Please Confirm your Email')
//                        ->htmlTemplate('registration/confirmation_email.html.twig')
//                );
                // do anything else you need here, like send an email

                return $this->redirectToRoute('user');
            }
            //dump($form->get('plainPassword')->getData());

            // ...
        }
        //dump($dataUser);
        //dump('tu as réussi presley');die();

        return new JsonResponse([
            'test' => 'test'
        ]);
    }
}
