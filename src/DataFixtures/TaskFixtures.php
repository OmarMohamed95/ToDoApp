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
            $list = new Tasks();
            $list->setTitle('task' . $i);
            $list->setNote('note' . $i);
            $list->setPriority(1);
            $list->setList($this->getReference(ListFixtures::getReferenceKey($i % ListFixtures::LIST_COUNT)));
            $list->setIsDone(0);
            $list->setIsNotified(0);
            $list->setRunAt(date("Y-m-d", strtotime('tomorrow')));

            $manager->persist($list);
            $this->addReference('list-' . $i, $list);
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