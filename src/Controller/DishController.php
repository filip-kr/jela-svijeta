<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\DataFormatService;
use App\Service\LanguageService;
use App\Service\PaginationService;

#[Route(
    path: '/api',
    name: 'api_dish',
    defaults: ['_format' => 'json'],
    methods: ['GET']
)]
class DishController extends AbstractController
{
    public function __invoke(
        Request $request,
        DishRepository $dishRepository,
        DataFormatService $dataFormatService,
        LanguageService $languageService,
        PaginationService $paginator
    ): JsonResponse 
    {
        if (!$languageService->isLanguageSet($request)) {
            return $this->json('lang parameter is required: \'en\' for English OR \'ja\' for Japanese');
        }

        $params = $request->query->all();
        $dishes = $dishRepository->findByParameters($params);
        $formattedData = $dataFormatService->formatData($dishes, $params);
        $totalItems = count($formattedData);

        $paginatedData = $paginator->paginate(
            $formattedData,
            isset($params['page']) ? $params['page'] : 1,
            isset($params['per_page']) ? $params['per_page'] : 10
        );

        $paginatedData = $dataFormatService->formatPaginatedData($paginatedData);

        $meta = $dataFormatService->getMetadata($params, $totalItems);
        $links = $dataFormatService->getLinks($request, $params);

        return $this->json([
            'meta' => $meta,
            'data' => $paginatedData,
            'links' => $links
        ]);
    }
}
