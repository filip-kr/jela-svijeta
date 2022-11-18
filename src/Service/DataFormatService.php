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
    ) {
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
        foreach ($dishTags as $dt) {
            $dt[0]->dishId = $dt['dishId'];
            array_push($dishTags, $dt[0]);
            array_shift($dishTags);
        }

        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['tags'] = [];

            foreach ($dishTags as $dt) {
                if ($isJapanese) {
                    $tagTranslation = $this->languageService->getOneTranslation($dt);
                }

                if ($this->formattedData[$i]['id'] == $dt->dishId) {
                    array_push(
                        $this->formattedData[$i]['tags'],
                        [
                            'id' => $dt->getId(),
                            'title' => $isJapanese ? $tagTranslation['ja']['title'] : $dt->getTitle(),
                            'slug' => $dt->getSlug()
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
        foreach ($dishIngredients as $di) {
            $di[0]->dishId = $di['dishId'];
            array_push($dishIngredients, $di[0]);
            array_shift($dishIngredients);
        }

        for ($i = 0; $i < count($this->rawData); $i++) {
            $this->formattedData[$i]['ingredients'] = [];

            foreach ($dishIngredients as $di) {
                if ($isJapanese) {
                    $ingredientTranslation = $this->languageService->getOneTranslation($di);
                }

                if ($this->formattedData[$i]['id'] == $di->dishId) {
                    array_push(
                        $this->formattedData[$i]['ingredients'],
                        [
                            'id' => $di->getId(),
                            'title' => $isJapanese ? $ingredientTranslation['ja']['title'] : $di->getTitle(),
                            'slug' => $di->getSlug()
                        ]
                    );
                }
            }
        }
    }
}
