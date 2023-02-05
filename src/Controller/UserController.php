<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\User1Type;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $hasAccess = $this->isGranted('ROLE_SUPER_ADMIN');
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        if ($this->getUser()->getId() !== $user->getId()){
            return $this->redirectToRoute('group_index');
        }
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_show', ['id'=> $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/login/new', name: 'userLogin')]
    public function userLogin(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $urlLogin = $this->generateUrl('login',[], UrlGeneratorInterface::ABSOLUTE_URL);
        $urlRegister = $this->generateUrl('user_api',[], UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->render('user/mainLogin.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'urlForm' => $urlLogin,
            'urlRegister' => $urlRegister
        ]);
    }

    #[Route('/registration/user', name: 'user_api')]
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

                return $this->redirectToRoute('userLogin');
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

    #[Route('/logout/new', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
