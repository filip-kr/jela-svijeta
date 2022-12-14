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
use Gedmo\Translatable\Entity\Translation;
use Faker\Factory;
use FakerRestaurant\Provider\en_US\Restaurant as EnglishRestaurant;
use FakerRestaurant\Provider\ja_JP\Restaurant as JapaneseRestaurant;
use Faker\Provider\ja_JP\Person;

class AppFixtures extends Fixture
{
    private $ingredients;
    private $tags;
    private $categories;
    private $dishes;

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
        $this->loadLanguages($manager);
        $this->loadIngredients($manager);
        $this->loadTags($manager);
        $this->loadCategories($manager);
        $this->loadDishes($manager);
        $this->loadDishTags($manager);
        $this->loadDishIngredients($manager);
    }

    private function loadLanguages($manager)
    {
        $english = new Language;
        $english->setLocale('en');
        $english->setLocalName('English');
        $manager->persist($english);

        $japanese = new Language;
        $japanese->setLocale('ja');
        $japanese->setLocalName('日本語');
        $manager->persist($japanese);

        $manager->flush();
    }

    private function loadIngredients($manager)
    {
        $ingredients = [];
        for ($i = 0; $i < 100; $i++) {
            $ingredient = new Ingredient;
            $ingredient->setTitle($this->faker->word());
            $ingredient->setSlug($this->faker->slug(2));

            $this->translator->translate($ingredient, 'title', 'ja', $this->kanaNameFaker->firstKanaName());
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        $this->ingredients = $ingredients;
        $manager->flush();
    }

    private function loadTags($manager)
    {
        $tags = [];
        for ($i = 0; $i < 100; $i++) {
            $tag = new Tag;
            $tag->setTitle($this->faker->word());
            $tag->setSlug($this->faker->slug(2));

            $this->translator->translate($tag, 'title', 'ja', $this->kanaNameFaker->firstKanaName());
            $tags[] = $tag;
            $manager->persist($tag);
        }

        $this->tags = $tags;
        $manager->flush();
    }

    private function loadCategories($manager)
    {
        $appetizer = new Category;
        $appetizer->setTitle('Appetizer');
        $appetizer->setSlug($this->faker->slug(2));
        $this->translator->translate($appetizer, 'title', 'ja', 'オードブル');
        $manager->persist($appetizer);

        $mainCourse = new Category;
        $mainCourse->setTitle('Main course');
        $mainCourse->setSlug($this->faker->slug(2));
        $this->translator->translate($mainCourse, 'title', 'ja', 'セコンド・ピアット');
        $manager->persist($mainCourse);

        $dessert = new Category;
        $dessert->setTitle('Dessert');
        $dessert->setSlug($this->faker->slug(2));
        $this->translator->translate($dessert, 'title', 'ja', 'デザート');
        $manager->persist($dessert);

        $this->categories = [$appetizer, $mainCourse, $dessert, NULL];
        $manager->flush();
    }

    private function loadDishes($manager)
    {
        $dishes = [];
        $statuses = ['created', 'modified', 'deleted'];
        for ($i = 0; $i < 60; $i++) {
            $dish = new Dish;

            $dish->setTitle($this->foodFakerEn->foodName());
            $dish->setDescription($this->faker->sentence());
            $dish->setStatus($statuses[rand(0, 2)]);
            $dish->setCreatedAt($this->faker->dateTime());
            $dish->setCategory($this->categories[rand(0, 3)]);

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

        $this->dishes = $dishes;
        $manager->flush();
    }

    private function loadDishTags($manager)
    {
        $addedTags = [];
        foreach ($this->dishes as $dish) {

            for ($i = 0; $i < rand(1, 6); $i++) {
                $randomSelector = rand(0, 99);
                $tag = $this->tags[$randomSelector];

                if (!in_array($tag, $addedTags)) {
                    $dishTag = $dish->addTag($tag);
                    $manager->persist($dishTag);
                    $addedTags[$i] = $tag;
                } else {
                    $i--;
                }
            }
        }

        $manager->flush();
    }

    private function loadDishIngredients($manager)
    {
        $addedIngredients = [];
        foreach ($this->dishes as $dish) {
            for ($i = 0; $i < rand(4, 12); $i++) {
                $randomSelector = rand(0, 99);
                $ingredient = $this->ingredients[$randomSelector];

                if (!in_array($ingredient, $addedIngredients)) {
                    $dishIngredient = $dish->addIngredient($ingredient);
                    $manager->persist($dishIngredient);
                    $addedIngredients[$i] = $ingredient;
                } else {
                    $i--;
                }
            }
        }

        $manager->flush();
    }
}
