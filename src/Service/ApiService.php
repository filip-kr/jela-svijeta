<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;

final class ApiService
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->translationsRepository = $this->entityManager->getRepository(Translation::class);
    }

    public function formatData($data, $params): array
    {
        $formattedData = [];
        $japanese = $this->isLanguageJapanese($params);

        if ($japanese) {
            $dishTranslations = $this->getTranslations($data);
        }

        for ($i = 0; $i < count($data); $i++) {
            $formattedData[] = [
                'id' => $data[$i]->getId(),
                'title' => $japanese ? $dishTranslations[$i]['ja']['title'] : $data[$i]->getTitle(),
                'description' => $japanese ? $dishTranslations[$i]['ja']['description'] : $data[$i]->getDescription(),
                'status' => $data[$i]->getStatus()
            ];
        }

        if (isset($params['with'])) {
            return $this->setAdditionalRequestData($data, $formattedData, $params, $japanese);
        }

        return $formattedData;
    }

    private function getTranslations($data): array // Additional service
    {
        $translations = [];

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]->setTranslatableLocale('ja');
            $translations[$i] = $this->translationsRepository->findTranslations($data[$i]);
        }

        return $translations;
    }

    private function isLanguageJapanese($params): bool
    {
        if (isset($params['lang']) && $params['lang'] == 'ja') {
            return true;
        }

        return false;
    }

    private function getCategories($data): array
    {
        $dishCategories = [];

        for ($i = 0; $i < count($data); $i++) {
            $dishCategories[$i] = $data[$i]->getCategory();
        }

        return $dishCategories;
    }

    private function setAdditionalRequestData($rawData, $formattedData, $params, $japanese)
    {
        $with = explode(',', $params['with']);

        if (in_array('category', $with)) {

            $dishCategories = $this->getCategories($rawData);

            if ($japanese) {
                $categoryTranslations = $this->getTranslations($dishCategories);
            }

            for ($i = 0; $i < count($rawData); $i++) {
                $formattedData[$i]['category'] = [];

                array_push(
                    $formattedData[$i]['category'],
                    [
                        'id' => $rawData[$i]->getCategory()->getId(),
                        'title' => $japanese ? $categoryTranslations[$i]['ja']['title'] : $rawData[$i]->getCategory()->getTitle(),
                        'slug' => $rawData[$i]->getCategory()->getSlug()
                    ]
                );
            }
        }

        return $formattedData;
    }
}
