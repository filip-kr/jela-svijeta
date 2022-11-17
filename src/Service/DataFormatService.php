<?php

namespace App\Service;

final class DataFormatService
{
    public function __construct(LanguageService $languageService)
    {
        $this->rawData = [];
        $this->formattedData = [];
        $this->languageService = $languageService;
    }

    public function formatData($data, $params): array
    {
        $this->rawData = $data;
        $isJapanese = $this->languageService->isLanguageJapanese($params);

        if ($isJapanese) {
            $dishTranslations = $this->languageService->getTranslations($data);
        }

        for ($i = 0; $i < count($data); $i++) {
            $this->formattedData[] = [
                'id' => $data[$i]->getId(),
                'title' => $isJapanese ? $dishTranslations[$i]['ja']['title'] : $data[$i]->getTitle(),
                'description' => $isJapanese ? $dishTranslations[$i]['ja']['description'] : $data[$i]->getDescription(),
                'status' => $data[$i]->getStatus()
            ];
        }

        if (isset($params['with'])) {
            $this->setAdditionalRequestData($params, $isJapanese);
        }

        return $this->formattedData;
    }

    private function setAdditionalRequestData($params, $isJapanese): void
    {
        $with = explode(',', $params['with']);

        if (in_array('category', $with)) {
            $this->setCategories($isJapanese);
        }
    }

    private function setCategories($isJapanese): void
    {
        $dishCategories = $this->getCategories($this->rawData);

        if ($isJapanese) {
            $categoryTranslations = $this->languageService->getTranslations($dishCategories);
        }

        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['category'] = [];

            array_push(
                $this->formattedData[$i]['category'],
                [
                    'id' => $this->rawData[$i]->getCategory()->getId(),
                    'title' => $isJapanese ? $categoryTranslations[$i]['ja']['title'] : $this->rawData[$i]->getCategory()->getTitle(),
                    'slug' => $this->rawData[$i]->getCategory()->getSlug()
                ]
            );
        }
    }

    private function getCategories($data): array
    {
        $dishCategories = [];

        for ($i = 0; $i < count($data); $i++) {
            $dishCategories[$i] = $data[$i]->getCategory();
        }

        return $dishCategories;
    }
}
