<?php

namespace App\Controller;

use App\Entity\GroupCategory;
use App\Form\GroupCategoryType;
use App\Repository\GroupCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/group/category')]
class GroupCategoryController extends AbstractController
{
    #[Route('/', name: 'group_category_index', methods: ['GET'])]
    public function index(GroupCategoryRepository $groupCategoryRepository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')){
            return  $this->redirectToRoute('group_index');
        }

        return $this->render('group_category/index.html.twig', [
            'group_categories' => $groupCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'group_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')){
            return  $this->redirectToRoute('group_index');
        }
        $groupCategory = new GroupCategory();
        $form = $this->createForm(GroupCategoryType::class, $groupCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($groupCategory);
            $entityManager->flush();

            return new JsonResponse([
                'code' => 'success',
                'url' => $this->generateUrl('group_index')
            ]);        }

        return $this->renderForm('group_category/new.html.twig', [
            'group_category' => $groupCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'group_category_show', methods: ['GET'])]
    public function show(GroupCategory $groupCategory): Response
    {
        return $this->render('group_category/show.html.twig', [
            'group_category' => $groupCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'group_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupCategory $groupCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupCategoryType::class, $groupCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('group_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group_category/edit.html.twig', [
            'group_category' => $groupCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'group_category_delete', methods: ['POST'])]
    public function delete(Request $request, GroupCategory $groupCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($groupCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
