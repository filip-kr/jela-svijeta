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

class AppFixtures extends Fixture
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->translator = $entityManager->getRepository(Translation::class);
        $this->faker = Factory::create();

        $this->foodFakerEn = Factory::create();
        $this->foodFakerEn->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($this->foodFakerEn));

        $this->foodFakerJap = Factory::create();
        $this->foodFakerJap->addProvider(new \FakerRestaurant\Provider\ja_JP\Restaurant($this->foodFakerJap));

        $this->kanaNameFaker = Factory::create();
        $this->kanaNameFaker->addProvider(new \Faker\Provider\ja_JP\Person($this->kanaNameFaker));
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
        $statuses = ['created', 'modified', 'deleted'];
        for ($i = 0; $i < 60; $i++) {
            $dish = new Dish;
            $dish->setTitle($this->foodFakerEn->foodName());
            $dish->setDescription($this->faker->sentence());
            $dish->setStatus($statuses[rand(0, 2)]);
            $dish->setCreatedAt($this->faker->dateTime());
            $dish->setCategory($categories[rand(0, 3)]);

            if ($dish->getStatus() == 'created') {
                if (rand(1, 100) > 50) {
                    $dish->setUpdatedAt($this->faker->dateTime());
                }
            }

            if ($dish->getStatus() == 'modified') {
                $dish->setUpdatedAt($this->faker->dateTime());
            }

            if ($dish->getStatus() == 'deleted') {
                $dish->setDeletedAt($this->faker->dateTime());

                if (rand(1, 100) > 50) {
                    $dish->setUpdatedAt($this->faker->dateTime());
                }
            }

            for ($j = 0; $j < rand(2, 12); $j++) {
                $dish->addIngredient($ingredients[rand(0, 99)]);
            }

            for ($k = 0; $k < rand(1, 6); $k++) {
                $dish->addTag($tags[rand(0, 99)]);
            }

            $this->translator->translate($dish, 'title', 'ja', $this->foodFakerJap->foodName());
            $this->translator->translate($dish, 'description', 'ja', $this->faker->sentence());

            $manager->persist($dish);
        }

        $manager->flush();
    }
}
