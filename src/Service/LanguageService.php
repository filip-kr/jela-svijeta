<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;

final class LanguageService
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->translationsRepository = $this->entityManager->getRepository(Translation::class);
    }

    public function isLanguageSet($request): bool
    {
        if (!$request->query->get('lang')) {
            return false;
        }

        return true;
    }

    public function isLanguageJapanese($params): bool
    {
        if (isset($params['lang']) && $params['lang'] == 'ja') {
            return true;
        }

        return false;
    }

    public function getTranslation($object): array
    {
        return $this->translationsRepository->findTranslations($object);
    }
}
