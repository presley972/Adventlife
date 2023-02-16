<?php

namespace App\Service;

use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentService
{
    public function __construct(
        private RequestStack $requestStack,
        private CommentRepository $commentRepository,
        private PaginatorInterface $paginator
    )
    {
    }

    public function getPaginatedComments($postId = null){

        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $post = $postId ? $postId : $request->query->getInt('blogPost', 1);
        $limit = 2;
        $commentsQuery = $this->commentRepository->findForPagination($post);



        return $this->paginator->paginate($commentsQuery, $page, $limit);
    }
}
