<?php

namespace Blogger\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Blogger\BlogBundle\Entity\Blog;

class BlogFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $blog = new Blog();
        $blog->setTitle('A day with Symfony2!!!');
        $blog->setBlog('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports');
        $blog->setAuthor(['0' => 'test']);
        $blog->setTags('symfony2, php, paradise, symblog');
        $manager->persist($blog);
        $manager->flush();

        $this->addReference('blog', $blog);
    }

    public function getOrder()
    {
        return 1;
    }
}