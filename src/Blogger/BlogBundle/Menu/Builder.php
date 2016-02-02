<?php

namespace Blogger\BlogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->addChild('Home', array('route' => 'sonata_admin_dashboard'));
        $menu->addChild('User');
        $menu['User']->addChild('Create user', ['route' => 'admin_blogger_blog_user_create']);
//        $menu->addChild('About Me', array('route' => 'about'));
//        $menu['About Me']->addChild('Edit profile', array('route' => 'edit_profile'));

        return $menu;
    }
}