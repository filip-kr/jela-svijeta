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
        $isSetWithTags = isset($params['with']) && str_contains($params['with'], 'tags') ? true : false;

        if ($isJapanese) {
            $dishTranslations = [];
            for ($i = 0; $i < count($data); $i++) {
                $dishTranslations[$i] = $this->languageService->getTranslation($data[$i]);
            }
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

        if (isset($params['tags'])) {
            $this->filterOutDishesWithMissingTags(explode(',', $params['tags']), $isJapanese, $isSetWithTags);
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

    private function getCategories(): array
    {
        $dishCategories = [];

        for ($i = 0; $i < count($this->rawData); $i++) {
            $dishCategories[$i] = $this->rawData[$i]->getCategory();
        }

        return $dishCategories;
    }

    private function setCategories($isJapanese): void
    {
        $dishCategories = $this->getCategories();

        if ($isJapanese) {
            $categoryTranslations = [];
            for ($i = 0; $i < count($dishCategories); $i++) {
                if ($dishCategories[$i]) {
                    $categoryTranslations[$i] = $this->languageService->getTranslation($dishCategories[$i]);
                }
            }
        }

        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['category'] = [];

            if ($this->rawData[$i]->getCategory()) {
                array_push(
                    $this->formattedData[$i]['category'],
                    [
                        'id' => $this->rawData[$i]->getCategory()->getId(),
                        'title' => $isJapanese ? $categoryTranslations[$i]['ja']['title'] : $this->rawData[$i]->getCategory()->getTitle(),
                        'slug' => $this->rawData[$i]->getCategory()->getSlug()
                    ]
                );
            } else {
                $this->formattedData[$i]['category'] = 'NULL';
            }
        }
    }

    private function getTags(): array
    {
        $dishIds = [];

        for ($i = 0; $i < count($this->formattedData); $i++) {
            $dishIds[$i] = $this->formattedData[$i]['id'];
        }

        return $this->tagRepository->findByDishId($dishIds);
    }

    private function setTags($isJapanese): void
    {
        $dishTags = $this->getTags();

        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['tags'] = [];

            foreach ($dishTags as $dt) {
                if ($isJapanese) {
                    $tagTranslation = $this->tagRepository->findOneBy(['id' => $dt['id']]);
                    $tagTranslation = $this->languageService->getTranslation($tagTranslation);
                }

                if ($this->formattedData[$i]['id'] == $dt['dishId']) {
                    array_push(
                        $this->formattedData[$i]['tags'],
                        [
                            'id' => $dt['id'],
                            'title' => $isJapanese ? $tagTranslation['ja']['title'] : $dt['title'],
                            'slug' => $dt['slug']
                        ]
                    );
                }
            }
        }
    }

    private function getIngredients(): array
    {
        $dishIds = [];

        for ($i = 0; $i < count($this->formattedData); $i++) {
            $dishIds[$i] = $this->formattedData[$i]['id'];
        }

        return $this->ingredientRepository->findByDishId($dishIds);
    }

    private function setIngredients($isJapanese): void
    {
        $dishIngredients = $this->getIngredients();

        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['ingredients'] = [];

            foreach ($dishIngredients as $di) {
                if ($isJapanese) {
                    $ingredientTranslation = $this->ingredientRepository->findOneBy(['id' => $di['id']]);
                    $ingredientTranslation = $this->languageService->getTranslation($di);
                }

                if ($this->formattedData[$i]['id'] == $di['dishId']) {
                    array_push(
                        $this->formattedData[$i]['ingredients'],
                        [
                            'id' => $di['id'],
                            'title' => $isJapanese ? $ingredientTranslation['ja']['title'] : $di['title'],
                            'slug' => $di['slug']
                        ]
                    );
                }
            }
        }
    }

    private function filterOutDishesWithMissingTags($tags, $isJapanese, $isSetWithTags): void
    {
        if (!$isSetWithTags) {
            $this->setTags($isJapanese);
        }

        $filteredDishes = [];
        foreach ($this->formattedData as $fd) {


            $fdTags = [];
            for ($i = 0; $i < count($fd['tags']); $i++) {
                $fdTags[$i] = $fd['tags'][$i]['id'];
            }

            if (count(array_intersect($fdTags, $tags)) == count($tags)) {
                if (!$isSetWithTags) {
                    unset($fd['tags']);
                }
                $filteredDishes[] = $fd;
            }
        }

        $this->formattedData = $filteredDishes;
    }
}
