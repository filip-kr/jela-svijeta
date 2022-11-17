<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use App\Entity\Dish;
use App\Entity\Ingredient;
use App\Entity\Language;
use App\Entity\Tag;
use App\Entity\DishTag;
use App\Entity\DishIngredient;
use Gedmo\Translatable\Entity\Translation;
use Faker\Factory;
use FakerRestaurant\Provider\en_US\Restaurant as EnglishRestaurant;
use FakerRestaurant\Provider\ja_JP\Restaurant as JapaneseRestaurant;
use Faker\Provider\ja_JP\Person;

class AppFixtures extends Fixture
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->translator = $entityManager->getRepository(Translation::class);
        $this->faker = Factory::create();

        $this->foodFakerEn = Factory::create();
        $this->foodFakerEn->addProvider(new EnglishRestaurant($this->foodFakerEn));

        $this->foodFakerJap = Factory::create();
        $this->foodFakerJap->addProvider(new JapaneseRestaurant($this->foodFakerJap));

        $this->kanaNameFaker = Factory::create();
        $this->kanaNameFaker->addProvider(new Person($this->kanaNameFaker));
    }

    public function load(ObjectManager $manager): void
    {
        // Languages
        // English
        $english = new Language;
        $english->setLocale('en');
        $english->setLocalName('English');
        $manager->persist($english);

        // Japanese
        $japanese = new Language;
        $japanese->setLocale('ja');
        $japanese->setLocalName('日本語');
        $manager->persist($japanese);

        // Ingredients
        $ingredients = [];
        for ($i = 0; $i < 100; $i++) {
            $ingredient = new Ingredient;
            $ingredient->setTitle($this->faker->word());
            $ingredient->setSlug($this->faker->slug(2));

            $this->translator->translate($ingredient, 'title', 'ja', $this->kanaNameFaker->firstKanaName());
            $ingredients[$i] = $ingredient;
            $manager->persist($ingredient);
        }

        // Tags
        $tags = [];
        for ($i = 0; $i < 100; $i++) {
            $tag = new Tag;
            $tag->setTitle($this->faker->word());
            $tag->setSlug($this->faker->slug(2));

            $this->translator->translate($tag, 'title', 'ja', $this->kanaNameFaker->firstKanaName());
            $tags[$i] = $tag;
            $manager->persist($tag);
        }

        // Categories
        // Appetizer
        $appetizer = new Category;
        $appetizer->setTitle('Appetizer');
        $appetizer->setSlug($this->faker->slug(2));
        $this->translator->translate($appetizer, 'title', 'ja', 'オードブル');
        $manager->persist($appetizer);

        // Main course
        $mainCourse = new Category;
        $mainCourse->setTitle('Main course');
        $mainCourse->setSlug($this->faker->slug(2));
        $this->translator->translate($mainCourse, 'title', 'ja', 'セコンド・ピアット');
        $manager->persist($mainCourse);

        // Dessert
        $dessert = new Category;
        $dessert->setTitle('Dessert');
        $dessert->setSlug($this->faker->slug(2));
        $this->translator->translate($dessert, 'title', 'ja', 'デザート');
        $manager->persist($dessert);

        $categories = [$appetizer, $mainCourse, $dessert, NULL];

        // Dishes
        $dishes = [];
        $statuses = ['created', 'modified', 'deleted'];
        for ($i = 0; $i < 60; $i++) {
            $dish = new Dish;

            $dish->setTitle($this->foodFakerEn->foodName());
            $dish->setDescription($this->faker->sentence());
            $dish->setStatus($statuses[rand(0, 2)]);
            $dish->setCreatedAt($this->faker->dateTime());
            $dish->setCategory($categories[rand(0, 3)]);

            if ($dish->getStatus() == 'modified') {
                $dish->setUpdatedAt($this->faker->dateTimeBetween($dish->getCreatedAt(), 'now'));
            }

            if ($dish->getStatus() == 'deleted') {
                if (rand(1, 100) > 50) {
                    $dish->setUpdatedAt($this->faker->dateTimeBetween($dish->getCreatedAt(), 'now'));
                }

                if ($dish->getUpdatedAt()) {
                    $dish->setDeletedAt($this->faker->dateTimeBetween($dish->getUpdatedAt(), 'now'));
                } else {
                    $dish->setDeletedAt($this->faker->dateTimeBetween($dish->getCreatedAt(), 'now'));
                }
            }

            $this->translator->translate(
                $dish,
                'title',
                'ja',
                $this->foodFakerJap->foodName()
            );
            $this->translator->translate($dish, 'description', 'ja', $this->faker->sentence());

            $manager->persist($dish);

            $dishes[] = $dish;
        }

        $manager->flush();

        // DishTag & DishIngredient
        $existingTags = [];
        $existingIngredients = [];
        foreach ($dishes as $dish) {

            for ($i = 0; $i < rand(1, 6); $i++) {
                $randomSelector = rand(0, 99);
                $tag = $tags[$randomSelector]->getId();

                if (!in_array($tag, $existingTags)) {
                    $dishTag = new DishTag;
                    $dishTag->setDishId($dish->getId());
                    $dishTag->setTagId($tag);
                    $existingTags[] = $tag;
                    $manager->persist($dishTag);
                }
            }

            for ($i = 0; $i < rand(4, 12); $i++) {
                $randomSelector = rand(0, 99);
                $ingredient = $ingredients[$randomSelector]->getId();

                if (!in_array($ingredient, $existingIngredients)) {
                    $dishIngredient = new DishIngredient;
                    $dishIngredient->setDishId($dish->getId());
                    $dishIngredient->setIngredientId($ingredient);
                    $existingIngredients[] = $ingredient;
                    $manager->persist($dishIngredient);
                }
            }
        }

        $manager->flush();
    }
}
