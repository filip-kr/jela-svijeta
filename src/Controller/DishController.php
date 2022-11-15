<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;

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
        EntityManagerInterface $entityManager
    ): JsonResponse 
    {
        if (!$this->isLanguageSetAndValid($request)) {
            return $this->json('lang parameter is required: \'en\' for English OR \'ja\' for Japanese');
        }

        $data = [];
        $dishes = $dishRepository->findAll();

        if ($request->query->get('lang') == 'en') {
            foreach ($dishes as $dish) {
                $data[] = [
                    'id' => $dish->getId(),
                    'title' => $dish->getTitle(),
                    'description' => $dish->getDescription(),
                    'status' => $dish->getStatus()
                ];
            }
        }

        if ($request->query->get('lang') == 'ja') {
            $translationsRepository = $entityManager->getRepository(Translation::class);

            foreach ($dishes as $dish) {
                $dish->setTranslatableLocale('ja');
                $translations = $translationsRepository->findTranslations($dish);

                $data[] = [
                    'id' => $dish->getId(),
                    'title' => $translations['ja']['title'],
                    'description' => $translations['ja']['description'],
                    'status' => $dish->getStatus()
                ];
            }
        }

        return $this->json($data);
    }

    private function isLanguageSetAndValid($request): bool
    {
        if (!$request->query->get('lang')) {
            return false;
        }

        return true;
    }
}
