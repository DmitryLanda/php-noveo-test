<?php
namespace NoveoTestBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $objects = \Nelmio\Alice\Fixtures::load(__DIR__.'/fixtures.yml', $manager);
    }
}
