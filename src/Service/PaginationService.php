<?php

namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;

final class PaginationService
{
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    public function paginate($array, $page = 1, $perPage = 10)
    {
        return $this->paginator->paginate($array, $page, $perPage);
    }
}
