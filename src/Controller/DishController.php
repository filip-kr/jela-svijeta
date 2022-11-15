<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
        DishRepository $dishRepository
    ): JsonResponse 
    {
        $dishes = $dishRepository->findBy(['status' => 'created']);

        $data = [];

        foreach ($dishes as $dish) {
            $data[] = [
                'id' => $dish->getId(),
                'title' => $dish->getTitle(),
                'description' => $dish->getDescription(),
                'status' => $dish->getStatus()
            ];
        }

        return $this->json([$data]);
    }
}
