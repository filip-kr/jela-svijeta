<?php

namespace App\Service;

use App\Repository\IngredientRepository;
use App\Repository\TagRepository;

final class DataFormatService
{
    public function __construct(
        LanguageService $languageService,
        IngredientRepository $ingredientRepository,
        TagRepository $tagRepository
    ) 
    {
        $this->rawData = [];
        $this->formattedData = [];
        $this->languageService = $languageService;
        $this->ingredientRepository = $ingredientRepository;
        $this->tagRepository = $tagRepository;
    }

    public function formatData($data, $params): array
    {
        $this->rawData = $data;
        $isJapanese = $this->languageService->isLanguageJapanese($params);

        if ($isJapanese) {
            foreach ($this->rawData as $dishEntity) {
                $dishTranslations[] = $this->languageService->getTranslation($dishEntity);
            }
        }

        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[] = [
                'id' => $this->rawData[$i]->getId(),
                'title' => $isJapanese ? $dishTranslations[$i]['ja']['title'] : $this->rawData[$i]->getTitle(),
                'description' => $isJapanese ? $dishTranslations[$i]['ja']['description'] : $this->rawData[$i]->getDescription(),
                'status' => $this->rawData[$i]->getStatus()
            ];
        }

        if (isset($params['with'])) {
            $this->setAdditionalRequestData($params, $isJapanese);
        }

        if (isset($params['tags'])) {
            $this->filterOutDishesWithMissingTags(explode(',', $params['tags']), $isJapanese);
        }

        return $this->formattedData;
    }

    private function setAdditionalRequestData($params, $isJapanese): void
    {
        $with = explode(',', $params['with']);

        if (in_array('category', $with)) {
            $this->setCategories($isJapanese);
        }

        if (in_array('tags', $with)) {
            $this->setTags($isJapanese);
        }

        if (in_array('ingredients', $with)) {
            $this->setIngredients($isJapanese);
        }
    }

    private function setCategories($isJapanese): DataFormatService
    {
        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['category'] = [];

            if ($this->rawData[$i]->getCategory()) {
                if ($isJapanese) {
                    $categoryTranslation = $this->languageService->getTranslation($this->rawData[$i]->getCategory());
                }

                array_push(
                    $this->formattedData[$i]['category'],
                    [
                        'id' => $this->rawData[$i]->getCategory()->getId(),
                        'title' => $isJapanese ? $categoryTranslation['ja']['title'] : $this->rawData[$i]->getCategory()->getTitle(),
                        'slug' => $this->rawData[$i]->getCategory()->getSlug()
                    ]
                );
            } else {
                $this->formattedData[$i]['category'] = NULL;
            }
        }

        return $this;
    }

    private function setTags($isJapanese): DataFormatService
    {
        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['tags'] = [];

            foreach ($this->rawData[$i]->getTags() as $dishTag) {
                if ($isJapanese) {
                    $tagTranslation = $this->languageService->getTranslation($dishTag);
                }

                array_push(
                    $this->formattedData[$i]['tags'],
                    [
                        'id' => $dishTag->getId(),
                        'title' => $isJapanese ? $tagTranslation['ja']['title'] : $dishTag->getTitle(),
                        'slug' => $dishTag->getSlug()
                    ]
                );
            }
        }

        return $this;
    }

    private function setIngredients($isJapanese): DataFormatService
    {
        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['ingredients'] = [];

            foreach ($this->rawData[$i]->getIngredients() as $dishIngredient) {
                if ($isJapanese) {
                    $ingredientTranslation = $this->languageService->getTranslation($dishIngredient);
                }

                array_push(
                    $this->formattedData[$i]['ingredients'],
                    [
                        'id' => $dishIngredient->getId(),
                        'title' => $isJapanese ? $ingredientTranslation['ja']['title'] : $dishIngredient->getTitle(),
                        'slug' => $dishIngredient->getSlug()
                    ]
                );
            }
        }

        return $this;
    }

    private function filterOutDishesWithMissingTags($tags): array
    {
        $filteredDishes = [];
        for ($i = 0; $i < count($this->formattedData); $i++) {
            $dishTagsIdArray = [];

            foreach ($this->rawData[$i]->getTags() as $dishTag) {
                $dishTagsIdArray[] = $dishTag->getId();
            }

            if (count(array_intersect($dishTagsIdArray, $tags)) == count($tags)) {
                $filteredDishes[] = $this->formattedData[$i];
            }
        }

        return $this->formattedData = $filteredDishes;
    }
}
