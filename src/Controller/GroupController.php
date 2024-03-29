<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Evenement;
use App\Entity\Group;
use App\Entity\GroupCategory;
use App\Entity\Image;
use App\Entity\Place;
use App\Entity\Prayer;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\BlogPostType;
use App\Form\EvenementType;
use App\Form\GroupType;
use App\Form\PrayerType;
use App\Repository\GroupRepository;
use App\Repository\PlaceRepository;
use App\Service\CommentService;
use App\Service\EvenementService;
use App\Service\FileUploader;
use App\Service\GroupService;
use App\Service\PrayerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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
    public function index(GroupService $groupService, PlaceRepository $placeRepository): Response
    {
        $places = $placeRepository->findPlacesForMapOnListGroup();

        return $this->render('group/index.html.twig',[
            'places' => $places,
            'groups' =>   $groupService->getPaginatedGroups(),
        ]);
    }

    #[Route('/lists', name: 'group_list', methods: ['POST'])]
    public function listGroup(GroupService $groupService)
    {

        return new JsonResponse([
            'code' => 'success',
            'html' => $this->renderView('group/listGroup.html.twig', [
                'groups' =>   $groupService->getPaginatedGroups(),
            ])
        ]);
    }
    #[Route('/lists/pagination', name: 'group_list_page', methods: ['POST'])]
    public function listGroupsPagination(Request $request,GroupService $groupService)
    {
        return new JsonResponse([
            'code' => 'success',
            'page' => $request->query->getInt('page') + 1,
            'html' => $this->renderView('group/listGroupPagination.html.twig', [
                'groups' =>   $groupService->getPaginatedGroups(),
            ])
        ]);
    }

    #[Route('/new', name: 'group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $group = new Group();
        $group->setOwner($user);

        $form = $this->createForm(GroupType::class, $group,[
        'action' => $this->generateUrl('group_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();
            $group->setCreatedAt(new \DateTimeImmutable());
            if ($form->get('place') !== null && $form->get('place')->getData() !== null && $form->get('place')->getData()->getAdress() !== null){
                $dataPlace = $form->get('place')->getData();
                $place = new Place();
                $place->setCreatedAt(new \DateTimeImmutable('now'));
                $place->setAdress($dataPlace->getAdress());
                $place->setCountry($dataPlace->getCountry());
                $place->setZipCode($dataPlace->getZipCode());
                $place->setLocality($dataPlace->getLocality());
                $place->setLat($dataPlace->getLat());
                $place->setLng($dataPlace->getLng());
                $place->setStreet($dataPlace->getStreet());
                $place->setStreetNumber($dataPlace->getStreetNumber());
                $place->setPlaceId($dataPlace->getPlaceId());
                $place->setAreaRegion($dataPlace->getAreaRegion());
                $place->addHomeGroup($group);
                $group->setPlace($place);
                $entityManager->persist($place);

            }
            if ($image !== null ){
                $fichier = $fileUploader->upload($image);
                // On crée l'image dans la base de données
                $img = new Image();
                $img->setImage($fichier);
                $group->setImage($img);

            }
            if ($form->get('groupCategories') !== null && $form->get('groupCategories')->getData() !== null){

                /** @var GroupCategory $category */
                foreach ($form->get('groupCategories')->getData() as $category){
                    $group->addGroupCategory($category);

                }
            }

            $entityManager->persist($group);
            $entityManager->flush();
            return new JsonResponse([
                'code' => 'success',
                'url' => $this->generateUrl('group_show', [ 'id' => $group->getId()])
            ]);

        }

        return $this->render('group/new.html.twig', [
                'group' => $group,
                'form' => $form->createView(),
            ]);
        /*return $this->renderForm('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);*/
    }

    #[Route('/{id}', name: 'group_show', methods: ['GET'])]
    public function show(Group $group, ManagerRegistry $doctrine, CommentService $commentService): Response
    {

        $user = $this->getUser();
        $member = $user ? $group->checkIfUserIsMember($user) : false;

        $events = $doctrine->getRepository(Evenement::class)->findByGroupAndMemberResultArray($group->getId(), $member);
        //
        foreach ($events as $key => $event){
            $events[$key]['start'] = $events[$key]['start']->Format('Y-m-d');
            $events[$key]['end'] = $events[$key]['end']->Format('Y-m-d');
            $events[$key]['url'] = $this->generateUrl('evenement_show',['evenement'=> $events[$key]['id'], 'group'=>$group->getId()]);
        }
        $comments = [];
        foreach ($group->getBlogPosts() as $post){
            $comments[$post->getId()] = $commentService->getPaginatedComments($post->getId());

        }

        return $this->render('group/show.html.twig', [
            'group' => $group,
            'events' => $events,
            'comments' => $comments,
            'member' => $member
        ]);
    }

    #[Route('/{id}/edit', name: 'group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Group $group, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        //dump($form);die();

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('place') !== null && $form->get('place')->getData() !== null && $form->get('place')->getData()->getAdress() !== null){

                if ($group->getPlace()){
                    $oldGroupPlace = $group->getPlace();
                    $oldGroupPlace->removeHomeGroup($group);
                    $entityManager->remove($oldGroupPlace);
                    $entityManager->flush();
                }
                $dataPlace = $form->get('place')->getData();
                $place = new Place();
                $place->setCreatedAt(new \DateTimeImmutable('now'));
                $place->setAdress($dataPlace->getAdress());
                $place->setCountry($dataPlace->getCountry());
                $place->setZipCode($dataPlace->getZipCode());
                $place->setLocality($dataPlace->getLocality());
                $place->setLat($dataPlace->getLat());
                $place->setLng($dataPlace->getLng());
                $place->setStreet($dataPlace->getStreet());
                $place->setStreetNumber($dataPlace->getStreetNumber());
                $place->setPlaceId($dataPlace->getPlaceId());
                $place->setAreaRegion($dataPlace->getAreaRegion());
                $place->addHomeGroup($group);
                $group->setPlace($place);
                $entityManager->persist($place);

            }
            $image = $form->get('image')->getData();
            if ($image !== null){
                if ($group->getImage()){
                    $oldGroupImage = $group->getImage();
                    $oldGroupImage->setGroupe(null);
                    $group->setImage(null);
                    $entityManager->persist($oldGroupImage);
                    $entityManager->flush();
                }
                $fichier = $fileUploader->upload($image);

                // On crée l'image dans la base de données
                $img = new Image();
                $img->setImage($fichier);
                $group->setImage($img);
            }
            if ($form->get('groupCategories') !== null && $form->get('groupCategories')->getData() !== null){

                $categories = $form->get('groupCategories')->getData();
                if ($group->getGroupCategories()){
                    foreach ($group->getGroupCategories() as $oldCategory){
                        if (!$categories->contains($oldCategory)){
                            $group->removeGroupCategory($oldCategory);
                        }
                    }
                }
                /** @var GroupCategory $category */
                foreach ($categories as $category){
                    $group->addGroupCategory($category);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('group_show', ['id' => $group->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token')) && ($group->getOwner()->getId() === $this->getUser()->getId() || $this->isGranted('ROLE_SUPER_ADMIN'))) {
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

        $notifications = [];
        foreach ($groups as $group){
            $notifications[$group->getId()] = $entityManager->getRepository(Subscription::class)->findByGroupAndUserCountSubscriber($user->getId(), $group->getId());

        }



        return $this->render('group/myGroups.html.twig', [
            'groups' => $groups,
            'user' => $user,
            'notifications' => $notifications
        ]);
    }


    #[Route('/my/groups/{group}/request', name: 'my_groups_request')]
    public function subscriptionRequestList(Request $request, EntityManagerInterface $entityManager, Group $group ){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        /** @var Subscription $subscriptions */
        $subscriptions = $entityManager->getRepository(Subscription::class)->findByGroupAndUser($user->getId(), $group->getId());
        foreach ($subscriptions as $subscription){
            /** @var $subscription Subscription */
            if ($subscription->getGroupStatut() === Subscription::SEND){
                $subscription->setGroupStatut(Subscription::WAIT);
                $subscription->setSubscriberStatut(Subscription::WAIT);
                $subscription->setNotificationChecked(true);
                $entityManager->persist($subscription);

            }
        }
        $entityManager->flush();

        $members = $entityManager->getRepository(User::class)->findMemberForGroup($group->getId());
        $blockedMembers = $entityManager->getRepository(User::class)->findBlockedMemberForGroup($group->getId());

        return $this->render('group/myGroups_request_member.html.twig', [
            'subscriptions' => $subscriptions,
            'group' => $group,
            'user' => $user,
            'members' => $members,
            'blockedMembers' => $blockedMembers,
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


        return $this->redirectToRoute('my_groups_request', ['group' => $group->getId()], Response::HTTP_SEE_OTHER);



    }

    #[Route('/my/groups/{group}/subscription/{subscription}/request/refuser', name: 'my_groups_request_refus')]
    public function refusMemberRequest(Request $request, EntityManagerInterface $entityManager, Group $group,Subscription $subscription){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($group->getOwner()->getId() !== $user->getId()){
            $this->redirectToRoute('user_show',['id' => $user->getId()]);
        }


        $subscription->setGroupStatut(Subscription::DENIED);
        $subscription->setGroupAcceptedAt(new \DateTimeImmutable());
        $subscription->setModifiedAt(new \DateTimeImmutable());

        $entityManager->persist($subscription);

        $entityManager->flush();


        return $this->redirectToRoute('my_groups_request', ['group' => $group->getId()], Response::HTTP_SEE_OTHER);



    }
    #[Route('/my/groups/{group}/subscription/{subscription}/request/delete', name: 'my_groups_request_delete')]
    public function deleteMemberRequest(Request $request, EntityManagerInterface $entityManager, Group $group,Subscription $subscription){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($group->getOwner()->getId() !== $user->getId()){
            $this->redirectToRoute('user_show',['id' => $user->getId()]);
        }

        $subscription->setGroupStatut(Subscription::BLOCKED);
        $subscription->setModifiedAt(new \DateTimeImmutable());
        $subscription->getSubscriber()->removeGroup($group);


        $entityManager->persist($subscription);
        $entityManager->persist($subscription->getSubscriber());
        $entityManager->persist($group);

        $entityManager->flush();


        return $this->redirectToRoute('my_groups_request', ['group' => $group->getId()], Response::HTTP_SEE_OTHER);



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
            'group' => $group,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/evenement/new', name: 'group_evenement_new', methods: ['GET', 'POST'])]
    public function addGroupEvenement(Request $request, Group $group, EntityManagerInterface $entityManager, FileUploader $fileUploader){
        $user = $this->getUser();
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->addEvenement($evenement);
            $evenement->setGroupe($group);
            //$user->addEvenement($evenement);

            $evenement->setCreatedAt(new \DateTimeImmutable());
            if ($form->get('place') !== null && $form->get('place')->getData()->getAdress() !== null){
                $dataPlace = $form->get('place')->getData();
                $place = new Place();
                $place->setCreatedAt(new \DateTimeImmutable('now'));
                $place->setAdress($dataPlace->getAdress());
                $place->setCountry($dataPlace->getCountry());
                $place->setZipCode($dataPlace->getZipCode());
                $place->setLocality($dataPlace->getLocality());
                $place->setLat($dataPlace->getLat());
                $place->setLng($dataPlace->getLng());
                $place->setStreet($dataPlace->getStreet());
                $place->setStreetNumber($dataPlace->getStreetNumber());
                $place->setPlaceId($dataPlace->getPlaceId());
                $place->setAreaRegion($dataPlace->getAreaRegion());
                $place->addEvenement($evenement);
                $evenement->setPlace($place);
                $entityManager->persist($place);

            }
            $evenement->setCreatedBy($user);

            $entityManager->persist($group);
            $entityManager->persist($user);
            $entityManager->persist($evenement);
            $entityManager->flush();

            return new JsonResponse([
                'code' => "success",
                'url' => $this->generateUrl('group_show', ['id' => $group->getId()])
            ]);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{group}/evenements', name: 'evenement_list_group', methods: ['GET'])]
    public function listEventForGroup(ManagerRegistry $doctrine,Group $group, EvenementService $evenementService): Response
    {
        $user = $this->getUser();
        $member = $group->checkIfUserIsMember($user);

        $eventsForCalendar = $doctrine->getRepository(Evenement::class)->findByGroupAndMemberResultArray($group->getId(), $member);
        $events = $evenementService->getPaginatedEvenement($group->getId(), $member);
        foreach ($eventsForCalendar as $key => $event){
            $eventsForCalendar[$key]['start'] = $eventsForCalendar[$key]['start']->Format('Y-m-d');
            $eventsForCalendar[$key]['end'] = $eventsForCalendar[$key]['end']->Format('Y-m-d');
            $eventsForCalendar[$key]['url'] = $this->generateUrl('evenement_show',['evenement'=> $eventsForCalendar[$key]['id'], 'group'=>$group->getId()]);
        }

        return $this->render('evenement/group_events_show.html.twig', [
            'events' => $events,
            'eventsForCalendar' => $eventsForCalendar,
            'group' => $group
        ]);
    }

    #[Route('/{id}/prayer/new', name: 'group_prayer_new', methods: ['GET', 'POST'])]
    public function addGroupPrayer(Request $request, Group $group, EntityManagerInterface $entityManager){
        $user = $this->getUser();
        $prayer =  new Prayer();
        $prayer->setOwner($user);
        $prayer->setGroupe($group);


        $form = $this->createForm(PrayerType::class, $prayer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $prayer->setCreatedAt(new \DateTimeImmutable());
            if ($form->get('place') !== null && $form->get('place')->getData()->getAdress() !== null){
                $dataPlace = $form->get('place')->getData();
                $place = new Place();
                $place->setCreatedAt(new \DateTimeImmutable('now'));
                $place->setAdress($dataPlace->getAdress());
                $place->setCountry($dataPlace->getCountry());
                $place->setZipCode($dataPlace->getZipCode());
                $place->setLocality($dataPlace->getLocality());
                $place->setLat($dataPlace->getLat());
                $place->setLng($dataPlace->getLng());
                $place->setStreet($dataPlace->getStreet());
                $place->setStreetNumber($dataPlace->getStreetNumber());
                $place->setPlaceId($dataPlace->getPlaceId());
                $place->setAreaRegion($dataPlace->getAreaRegion());
                $place->addPrayer($prayer);
                $entityManager->persist($place);

            }


            $entityManager->persist($prayer);
            $entityManager->flush();

            return new JsonResponse([
                'code' => 'success',
                'url' => $this->generateUrl('group_show', [ 'id' => $group->getId()])
            ]);
        }

        return $this->renderForm('prayer/new.html.twig', [
            'prayer' => $prayer,
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{group}/prayers', name: 'prayer_list_group', methods: ['GET'])]
    public function listPrayesForGroup(ManagerRegistry $doctrine,Group $group, PrayerService $prayerService): Response
    {
        $user = $this->getUser();
        $member = $group->checkIfUserIsMember($user);

        $prayers = $prayerService->getPaginatedPrayerForGroup($group->getId(), $member);

        return $this->render('prayer/group_prayer_list.html.twig', [
            'prayers' => $prayers,
            'group' => $group
        ]);
    }
    #[Route('/group/search', name: 'group_search')]
    public function searchGroup(Request $request, EntityManagerInterface $entityManager, PlaceRepository $placeRepository)
    {
        $places = $placeRepository->findPlacesForMapOnListGroup();

        return $this->render('group/searchPage.html.twig', [
            'places' => $places,
            ]);
    }
}
