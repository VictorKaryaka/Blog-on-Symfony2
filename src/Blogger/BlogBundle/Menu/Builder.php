<?php

namespace Blogger\BlogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->addChild('Home', ['route' => 'sonata_admin_dashboard']);
        $menu->addChild('User');
        $menu['User']->addChild('Create user', ['route' => 'admin_blogger_blog_user_create']);
        $menu['User']->addChild('Users list', ['route' => 'admin_blogger_blog_user_list']);
        $menu->addChild('Comment');
        $menu['Comment']->addChild('Create comment', ['route' => 'admin_blogger_blog_comment_create']);
        $menu['Comment']->addChild('Comments list', ['route' => 'admin_blogger_blog_comment_list']);
        $menu->addChild('Blog');
        $menu['Blog']->addChild('Create blog', ['route' => 'admin_blogger_blog_blog_create']);
        $menu['Blog']->addChild('Blogs list', ['route' => 'admin_blogger_blog_blog_list']);
        $menu->addChild('Settings', ['route' => 'blogger_blog_admin_settings']);

        return $menu;
    }
}