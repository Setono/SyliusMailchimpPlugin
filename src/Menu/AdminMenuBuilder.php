<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuBuilder
{
    public function addSection(MenuBuilderEvent $event): void
    {
        $header = $this->getHeader($event->getMenu());

        $header
            ->addChild('audiences', [
                'route' => 'setono_sylius_mailchimp_admin_audience_index', // todo should be the route to audience index
            ])
            ->setLabel('setono_sylius_mailchimp.menu.admin.main.mailchimp.audiences')
            ->setLabelAttribute('icon', 'users')
        ;
    }

    private function getHeader(ItemInterface $menu): ItemInterface
    {
        $header = $menu->getChild('mailchimp');
        if (null !== $header) {
            return $header;
        }

        $header = $menu->addChild('mailchimp')
            ->setLabel('setono_sylius_mailchimp.menu.admin.main.mailchimp.header')
        ;

        return $header;
    }
}
