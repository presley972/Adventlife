<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FinalTestController extends AbstractController
{
    #[Route('/final/test', name: 'final_test')]
    public function index(): Response
    {
        return $this->render('final_test/index.html.twig', [
            'controller_name' => 'FinalTestController',
        ]);
    }
}
