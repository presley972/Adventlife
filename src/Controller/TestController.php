<?php

namespace App\Controller;

use App\Entity\Test;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $test = new Test();
        $test->setTitle('oui');
        $test->setComment('enfin jai réussie super');
        $entityManager->persist($test);
        $entityManager->flush();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
            'presley' => 'tu gère',
            'test' => $test->getId()
        ]);
    }

    #[Route('/test/detail/{test}', name: 'detail_show')]
    public function detail(ManagerRegistry $doctrine, Test $test): Response
    {
        return $this->json([
            'title' => $test->getTitle(),
            'commentaire' => $test->getComment()
        ]);
    }
}
