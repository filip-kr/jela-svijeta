<?php

namespace App\Service;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;

final class PaginationService
{
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    public function paginate($array, $page = 1, $perPage = 10): SlidingPagination
    {
        if (!$this->isParametersValid($page, $perPage)) {
            $page = 1;
            $perPage = 10;
        }

        return $this->paginator->paginate($array, $page, $perPage);
    }

    public function formatPaginatedData($dataArray): array
    {
        $paginatedData = [];
        foreach ($dataArray as $da) {
            $paginatedData[] = $da;
        }

        return $paginatedData;
    }

    public function getMetadata($paginatedData): array
    {
        return $metadata = [
            'currentPage' => $paginatedData->getCurrentPageNumber(),
            'totalItems' => $paginatedData->getTotalItemCount(),
            'itemsPerPage' => $paginatedData->getItemNumberPerPage(),
            'totalPages' => $paginatedData->getPageCount()
        ];
    }

    public function getLinks($request, $params, $metadata): array
    {
        $uri = $request->getUri();

        if (!isset($params['page'])) {
            $prev = $uri . '&page=' . $metadata['currentPage'] - 1;
            if ($metadata['currentPage'] - 1 == 0) {
                $prev = NULL;
            }

            $next = $uri . '&page=' . $metadata['currentPage'] + 1;
            if ($metadata['currentPage'] + 1 > $metadata['totalPages']) {
                $next = NULL;
            }

            $self = $uri . '&page=' . $metadata['currentPage'];
        } else {
            $prev = preg_replace('/page=(.?)/', 'page=' . $metadata['currentPage'] - 1, $uri);
            if ($metadata['currentPage'] - 1 == 0) {
                $prev = NULL;
            }

            $next = preg_replace('/page=(.?)/', 'page=' . $metadata['currentPage'] + 1, $uri);
            if ($metadata['currentPage'] + 1 > $metadata['totalPages']) {
                $next = NULL;
            }

            $self = preg_replace('/page=(.?)/', 'page=' . $metadata['currentPage'], $uri);
        }

        return $links = [
            'prev' => $prev,
            'next' => $next,
            'self' => $self
        ];
    }

    private function isParametersValid($page, $perPage): bool
    {
        if (!is_numeric($page) || $page <= 0) {
            return false;
        }

        if (!is_numeric($perPage) || $perPage <= 0) {
            return false;
        }

        return true;
    }
}
