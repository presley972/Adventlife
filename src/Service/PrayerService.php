<?php

namespace App\Service;

use App\Repository\PrayerRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PrayerService
{
    public function __construct(
        private RequestStack $requestStack,
        private PrayerRepository $prayerRepository,
        private PaginatorInterface $paginator
    )
    {
    }

    public function getPaginatedPrayerForGroup($group = null, $member = null){

        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $group = $group ? $group : $request->query->getInt('group', 1);
        $member = $member ? $member : $request->query->getInt('member', 1);
        $limit = 5;
        $eventsQuery = $this->prayerRepository->findForPaginationGroup($group,$member);



        return $this->paginator->paginate($eventsQuery, $page, $limit);
    }

}
