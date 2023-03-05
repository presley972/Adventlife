<?php

namespace App\Service;

use App\Repository\GroupRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class GroupService
{

    public function __construct(
        private RequestStack $requestStack,
        private GroupRepository $groupRepository,
        private PaginatorInterface $paginator
    )
    {
    }

    public function getPaginatedGroups(){

        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = 5;
        $groupsQuery = $this->groupRepository->findForPagination();

        //$groupsQuery = $this->groupRepository->findBy([],['createdAt' => 'desc']);


        return $this->paginator->paginate($groupsQuery, $page, $limit);
    }
}
