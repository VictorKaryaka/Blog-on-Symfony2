<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\Comment;

class CommentFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $comment = new Comment();
        $comment->setUser('test');
        $comment->setComment('To make a long story short. You can\'t go wrong by choosing Symfony!');
        $comment->setUser($manager->merge($this->getReference('user-test')));
        $comment->setBlog($manager->merge($this->getReference('blog')));
        $manager->persist($comment);

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}