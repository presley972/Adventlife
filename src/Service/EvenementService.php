<?php

namespace App\Service;

use App\Repository\CommentRepository;
use App\Repository\EvenementRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class EvenementService
{
    public function __construct(
        private RequestStack $requestStack,
        private EvenementRepository $evenementRepository,
        private PaginatorInterface $paginator
    )
    {
    }

    public function getPaginatedEvenement($group = null, $member = null){

        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $group = $group ? $group : $request->query->getInt('group', 1);
        $member = $member ? $member : $request->query->getInt('member', 1);
        $limit = 5;
        $eventsQuery = $this->evenementRepository->findForPagination($group,$member);



        return $this->paginator->paginate($eventsQuery, $page, $limit);
    }
}
