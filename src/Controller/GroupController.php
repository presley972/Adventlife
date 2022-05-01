<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Group;
use App\Entity\Image;
use App\Entity\Subscription;
use App\Form\BlogPostType;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/groups')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'group_index', methods: ['GET'])]
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $group = new Group();
        $group->setOwner($user);

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();
            $group->setCreatedAt(new \DateTimeImmutable());


            $fichier = $fileUploader->upload($image);

            // On crée l'image dans la base de données
            $img = new Image();
            $img->setImage($fichier);
            $group->setImage($img);
            $entityManager->persist($group);
            $entityManager->flush();
            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'group_show', methods: ['GET'])]
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    #[Route('/{id}/edit', name: 'group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Group $group, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if ($image){
                $fichier = $fileUploader->upload($image);

                // On crée l'image dans la base de données
                $img = new Image();
                $img->setImage($fichier);
                $group->setImage($img);
            }

            $entityManager->flush();

            return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{group}/subscribe/request', name: 'subscribe_request')]
    public function groupRequest(Request $request, EntityManagerInterface $entityManager, Group $group){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if ($entityManager->getRepository(Subscription::class)->findBy(["groupe" => $group,"subscriber" => $user]) ){
            return new JsonResponse([
                'reponse' => 'already friends'
            ]);
        }

        $subscription = new Subscription();
        $subscription->setGroupe($group);
        $subscription->setSubscriber($user);
        $subscription->setCreatedAt(new \DateTimeImmutable());
        $subscription->setNotificationChecked(false);
        $subscription->setGroupStatut(Subscription::SEND);
        $subscription->setSubscriberStatut(Subscription::WAIT);
        $entityManager->persist($subscription);
        $entityManager->flush();

        $array['voila'] = 'voila';


        return new JsonResponse([
            'reponse' => "Request send",
            'request' => $array
        ]);

    }

    #[Route('/my/groups', name: 'my_groups')]
    public function myGroups(Request $request, EntityManagerInterface $entityManager){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $groups = $entityManager->getRepository(Group::class)->findBy(['owner'=>$user]);

        return $this->render('group/myGroups.html.twig', [
            'groups' => $groups
        ]);
    }


    #[Route('/my/groups/{group}/request', name: 'my_groups_request')]
    public function subscriptionRequestList(Request $request, EntityManagerInterface $entityManager, Group $group ){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        /** @var Subscription $subscriptions */
        $subscriptions = $entityManager->getRepository(Subscription::class)->findByGroupAndUser($user->getId(), $group->getId());

        return $this->render('group/myGroups_request_member.html.twig', [
            'subscriptions' => $subscriptions,
            'group' => $group
        ]);
    }

    #[Route('/my/groups/{group}/subscription/{subscription}/request', name: 'my_groups_request_accept')]
    public function acceptMemberRequest(Request $request, EntityManagerInterface $entityManager, Group $group,Subscription $subscription){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $subscription->setGroupStatut(Subscription::ACCEPTED);
        $subscription->setSubscriberStatut(Subscription::ACCEPTED);
        $subscription->setGroupAcceptedAt(new \DateTimeImmutable());
        $subscription->setSubscriberAcceptedAt(new \DateTimeImmutable());
        $subscription->setModifiedAt(new \DateTimeImmutable());

        $subscription->getSubscriber()->addGroup($group);

        $entityManager->persist($subscription);
        $entityManager->persist($subscription->getSubscriber());
        $entityManager->persist($group);

        $entityManager->flush();


        return new JsonResponse([
            'statut' => 'success'
        ]);


    }

    #[Route('/{id}/blog/post/new', name: 'group_blog_post_new', methods: ['GET', 'POST'])]
    public function addGroupPost(Request $request, Group $group, EntityManagerInterface $entityManager, FileUploader $fileUploader){
        $user = $this->getUser();
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->addBlogPost($blogPost);
            $user->addBlogPost($blogPost);
            /** @var UploadedFile $image */
            $images = $form->get('images')->getData();
            $blogPost->setCreatedAt(new \DateTimeImmutable());

            foreach ($images as $image){
                $fichier = $fileUploader->upload($image);

                // On crée l'image dans la base de données
                $img = new Image();
                $img->setImage($fichier);
                $blogPost->addImage($img);
                $entityManager->persist($img);
            }

            $entityManager->persist($group);
            $entityManager->persist($user);
            $entityManager->persist($blogPost);
            $entityManager->flush();

            return $this->redirectToRoute('group_show', ['id' => $group->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog_post/new.html.twig', [
            'blog_post' => $blogPost,
            'form' => $form,
        ]);
    }
}
