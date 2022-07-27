<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $task = new Task();
        $task->setName("Redaction du CDC")
            ->setDescription("blablbla")
            ->setDeadline()

        $manager->persist($task);

        $manager->flush();
    }
}
