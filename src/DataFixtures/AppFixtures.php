<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Dish;
use App\Entity\Ingredient;
use App\Entity\Language;
use App\Entity\Tag;
use Faker\Factory;
use Gedmo\Translatable\Entity\Translation;

class AppFixtures extends Fixture
{
    private $translator;
    private $faker;
    private $kanaNameFaker;
    private $foodFakerEn;
    private $foodFakerJap;

    public function __construct()
    {
        $this->translator = new Translation;
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

        $manager->flush();
    }
}
