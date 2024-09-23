<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $task = (new Task())
                ->setTitle('Task ' . $i)
                ->setDescription('Description ' . $i)
                ->setEstimatedDuration(45)
                ->setProject($this->getReference(ProjectFixtures::PROJECTS[$i]));

            $manager->persist($task);
        }

        $manager->flush();
    }
}
