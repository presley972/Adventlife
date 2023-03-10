<?php

namespace App\Controller;

use App\Entity\Place;
use App\Entity\Prayer;
use App\Form\PrayerType;
use App\Repository\PlaceRepository;
use App\Repository\PrayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prayer')]
class PrayerController extends AbstractController
{
    #[Route('/', name: 'prayer_index', methods: ['GET'])]
    public function index(PrayerRepository $prayerRepository, PlaceRepository $placeRepository): Response
    {
        $places = $placeRepository->findPlacesForMapOnListPrayer();

        return $this->render('prayer/index.html.twig', [
            'prayers' => $prayerRepository->findAll(),
            'places' => $places
        ]);
    }

    #[Route('/new', name: 'prayer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $prayer =  new Prayer();
        $prayer->setOwner($user);


        $form = $this->createForm(PrayerType::class, $prayer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $prayer->setCreatedAt(new \DateTimeImmutable());
            if ($form->get('place') !== null && $form->get('place')->getData()->getAdress() !== null) {
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
                'url' => $this->generateUrl('prayer_index')
            ]);
        }

            return $this->renderForm('prayer/new.html.twig', [
            'prayer' => $prayer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'prayer_show', methods: ['GET'])]
    public function show(Prayer $prayer): Response
    {
        return $this->render('prayer/show.html.twig', [
            'prayer' => $prayer,
        ]);
    }

    #[Route('/{id}/edit', name: 'prayer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Prayer $prayer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrayerType::class, $prayer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('prayer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('prayer/edit.html.twig', [
            'prayer' => $prayer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'prayer_delete', methods: ['POST'])]
    public function delete(Request $request, Prayer $prayer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prayer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($prayer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('prayer_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/addMember', name: 'prayer_membership_request')]
    public function prayMemberShips(Request $request, EntityManagerInterface $entityManager, Prayer $prayer)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $prayer->addMembership($user);
        $array['voila'] = 'voila';

        $entityManager->persist($prayer);
        $entityManager->flush();

        return new JsonResponse([
            'reponse' => "Request send",
            'request' => $array
        ]);

    }
}
