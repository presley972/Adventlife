<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Subscription;
use App\Form\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    #[Route('/group', name: 'group')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groups = $entityManager->getRepository(Group::class)->findAll();

        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $group->setCreatedAt(new \DateTimeImmutable());
            $group->setOwner($this->getUser());
            $entityManager->persist($group);
            $entityManager->flush();
        }

            return $this->render('group/index.html.twig', [
            'controller_name' => 'GroupController',
            'form' => $form->createView(),
            'groups' => $groups
        ]);
    }


    #[Route('/group/{group}/subscribe/request', name: 'subscribe_request')]
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
//        $array['request']['id'] = $friend->getId();
//        $array['request']['createdAt'] = $friend->getCreatedAt();
//        $array['request']['checked'] = $friend->getNotificationChecked();
//        $array['userOne']['info'] ['id']= $friend->getUserOne()->getId();
//        $array['userOne']['info'] ['username']= $friend->getUserOne()->getUsername();
//        $array['userOne']['info'] ['pseudo']= $friend->getUserOne()->getPseudo();
//        $array['userOne']['Statut'] = $friend->getUserOneStatut();
//        $array['userTwo']['info'] ['id']= $friend->getUserTwo()->getId();
//        $array['userTwo']['info'] ['username']= $friend->getUserTwo()->getUsername();
//        $array['userTwo']['info'] ['pseudo']= $friend->getUserTwo()->getPseudo();
//        $array['userTwo']['Statut'] = $friend->getUserTwoStatut();


        return new JsonResponse([
            'reponse' => "Request send",
            'request' => $array
        ]);

    }
}
