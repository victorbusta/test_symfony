<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\CarCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Get the car category
        $category = new CarCategory();
        $category->setName('family');
        $manager->persist($category);

        $categorybis = new CarCategory();
        $categorybis->setName('sport');
        $manager->persist($categorybis);

        $manager->flush();

        for ($i = 1; $i <= 50; ++$i) {
            $car = new Car();
            $car->setName("Car $i");
            $car->setNbSeats(rand(2, 5));
            $car->setNbDoors(4);
            $car->setCost(rand(10000, 50000));
            $car->setCarCategory(0 === $i % 2 ? $categorybis : $category);

            $manager->persist($car);
        }

        $manager->flush();
    }
}
