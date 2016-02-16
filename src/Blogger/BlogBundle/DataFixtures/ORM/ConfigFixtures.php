<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\Config;

class ConfigFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $config = new Config();
        $config->setBlogsLimit(5);
        $config->setCommentsLimit(6);
        $config->setContactEmail('test@test.com');
        $manager->persist($config);
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}