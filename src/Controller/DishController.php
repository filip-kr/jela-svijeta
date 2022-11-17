<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;

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
        ApiService $apiService
    ): JsonResponse 
    {
        if (!$this->isLanguageSet($request)) {
            return $this->json('lang parameter is required: \'en\' for English OR \'ja\' for Japanese');
        }

        $params = $request->query->all();
        $dishes = $dishRepository->findByParameters($params);
        $data = $apiService->formatData($dishes, $params);

        return $this->json($data);
    }

    private function isLanguageSet($request): bool
    {
        if (!$request->query->get('lang')) {
            return false;
        }

        return true;
    }
}
