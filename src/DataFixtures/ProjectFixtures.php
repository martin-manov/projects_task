<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public const PROJECTS = [0,1,2,3,4];

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $project = (new Project())
                ->setTitle('Project ' . $i)
                ->setDescription('Description ' . $i);


            $this->addReference(self::PROJECTS[$i], $project);
            $manager->persist($project);
        }

        $manager->flush();
    }
}
