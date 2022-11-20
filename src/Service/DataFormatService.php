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
        // dd($data);
        $this->rawData = $data;
        $isJapanese = $this->languageService->isLanguageJapanese($params);
        $isSetWithTags = isset($params['with']) && str_contains($params['with'], 'tags') ? true : false; // ???

        if ($isJapanese) {
            foreach ($this->rawData as $dishEntity) {
                $dishTranslations[] = $this->languageService->getTranslation($dishEntity);
            }

            // dd($dishTranslations);


            // $dishTranslations = [];
            // for ($i = 0; $i < count($data); $i++) {
            //     $dishTranslations[$i] = $this->languageService->getTranslation($data[$i]);
            // }
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

    // private function getCategories(): array
    // {
    //     $dishCategories = [];

    //     for ($i = 0; $i < count($this->rawData); $i++) {
    //         $dishCategories[$i] = $this->rawData[$i]->getCategory();
    //     }

    //     return $dishCategories;
    // }

    private function setCategories($isJapanese): void
    {
        // $dishCategories = $this->getCategories();

        // if ($isJapanese) {
        //     $categoryTranslations = [];
        //     foreach ($this->rawData as $dishEntity) {
        //         if ($dishEntity->getCategory()) {
        //             $categoryTranslations[] = $this->languageService->getTranslation($dishEntity->getCategory());
        //         }
        //     }
        // }

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
                $this->formattedData[$i]['category'] = 'NULL';
            }
        }
    }

    // private function getTags(): array
    // {
    //     $dishIds = [];

    //     for ($i = 0; $i < count($this->formattedData); $i++) {
    //         $dishIds[$i] = $this->formattedData[$i]['id'];
    //     }

    //     return $this->tagRepository->findByDishId($dishIds);
    // }

    private function setTags($isJapanese): void
    {
        // $dishTags = $this->getTags();
        for ($i = 0; $i < count($this->rawData); $i++) {
            foreach ($this->rawData[$i]->getTags() as $dishTag) {
                if ($isJapanese) {
                    // $tagTranslations[] = $this->tagRepository->findOneBy(['id' => $dt['id']]);
                    $tagTranslation = $this->languageService->getTranslation($dishTag);
                }

                $this->formattedData[$i]['tags'] = [
                    'id' => $dishTag->getId(),
                    'title' => $isJapanese ? $tagTranslation['ja']['title'] : $dishTag->getTitle(),
                    'slug' => $dishTag->getSlug()
                ];
            }
        }








        // for ($i = 0; $i < count($this->rawData); $i++) {
        //     $this->formattedData[$i]['tags'] = [];

        //     foreach ($this->rawData->getTags() as $dishTag) {
        //         $tagTranslations = [];
        //         if ($isJapanese) {
        //             // $tagTranslations[] = $this->tagRepository->findOneBy(['id' => $dt['id']]);
        //             $tagTranslations[] = $this->languageService->getTranslation($dishTag);
        //         }

        //         if ($this->formattedData[$i]['id'] == $dishTag->getDishes()->getId()) {
        //             array_push(
        //                 $this->formattedData[$i]['tags'],
        //                 [
        //                     'id' => $dt['id'],
        //                     'title' => $isJapanese ? $tagTranslation['ja']['title'] : $dt['title'],
        //                     'slug' => $dt['slug']
        //                 ]
        //             );
        //         }
        //     }
        // }
    }

    // private function getIngredients(): array
    // {
    //     $dishIds = [];

    //     for ($i = 0; $i < count($this->formattedData); $i++) {
    //         $dishIds[$i] = $this->formattedData[$i]['id'];
    //     }

    //     return $this->ingredientRepository->findByDishId($dishIds);
    // }

    private function setIngredients($isJapanese): void
    {
        for ($i = 0; $i < count($this->rawData); $i++) {
            foreach ($this->rawData[$i]->getIngredients() as $dishIngredient) {
                if ($isJapanese) {
                    // $tagTranslations[] = $this->tagRepository->findOneBy(['id' => $dt['id']]);
                    $ingredientTranslation = $this->languageService->getTranslation($dishIngredient);
                }

                $this->formattedData[$i]['ingredients'] = [
                    'id' => $dishIngredient->getId(),
                    'title' => $isJapanese ? $ingredientTranslation['ja']['title'] : $dishIngredient->getTitle(),
                    'slug' => $dishIngredient->getSlug()
                ];
            }
        }
    }

    private function filterOutDishesWithMissingTags($tags, $isJapanese, $isSetWithTags): void
    {

        $filteredDishes = [];
        foreach ($this->rawData as $dishEntity) {

            $dishTagsIdArray = [];
            foreach($dishEntity->getTags() as $dishTag) {
                $dishTagsIdArray[] = $dishTag->getId();
            }


            if (count(array_intersect($dishTagsIdArray, $tags)) == count($tags)) {
                $filteredDishes[] = $dishEntity;
            }
        }
        dd($dishTagsIdArray);
        dd($filteredDishes);

        $this->formattedData = $filteredDishes;
    }
}
