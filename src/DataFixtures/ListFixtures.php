<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Lists;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ListFixtures extends Fixture implements DependentFixtureInterface
{
    public const LIST_COUNT = 5;

    public static function getReferenceKey($i)
    {
        return sprintf('list%s', $i);
    }

    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < self::LIST_COUNT; $i++) {
            $list = new Lists();
            $list->setTitle('list'. $i);
            $list->setUser($this->getReference(UserFixtures::USER_REFERENCE));

            $manager->persist($list);
            $this->addReference(self::getReferenceKey($i), $list);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}