<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Tasks;
use App\DataFixtures\ListFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i=0; $i < 10; $i++) {
            $task = new Tasks();
            $task->setTitle('task' . $i);
            $task->setNote('note' . $i);
            $task->setPriority(1);
            $task->setList($this->getReference(ListFixtures::getReferenceKey($i % ListFixtures::LIST_COUNT)));
            $task->setIsDone(0);
            $task->setIsNotified(0);
            $task->setRunAt(date("Y-m-d", strtotime('tomorrow')));

            $manager->persist($task);
            $this->addReference('task-' . $i, $task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ListFixtures::class,
        );
    }
}