<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\User;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('test');
        $user->setEmail('test@test.com');
        $user->setEnabled(1);
        $user->setPlainPassword(111);
        $manager->persist($user);
        $manager->flush();

        $this->addReference('user-test', $user);
    }

    public function getOrder()
    {
        return 2;
    }
}