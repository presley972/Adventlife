<?php

namespace App\Controller;

use App\Entity\Prayer;
use App\Form\PrayerType;
use App\Repository\PrayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prayer')]
class PrayerController extends AbstractController
{
    #[Route('/', name: 'prayer_index', methods: ['GET'])]
    public function index(PrayerRepository $prayerRepository): Response
    {
        return $this->render('prayer/index.html.twig', [
            'prayers' => $prayerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'prayer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prayer = new Prayer();
        $form = $this->createForm(PrayerType::class, $prayer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prayer);
            $entityManager->flush();

            return $this->redirectToRoute('prayer_index', [], Response::HTTP_SEE_OTHER);
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
}
