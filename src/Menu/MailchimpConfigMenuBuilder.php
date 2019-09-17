<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class MailchimpConfigMenuBuilder
{
    public function addConfigItem(MenuBuilderEvent $event): void
    {
        $configurationMenu = $event->getMenu()->getChild('configuration');

        if (null === $configurationMenu) {
            return;
        }

        $configurationMenu
            ->addChild('setono_mailchimp_config', [
                'route' => 'setono_sylius_mailchimp_admin_config_index',
            ])
            ->setLabel('setono_sylius_mailchimp.ui.menu.config_menu')
            ->setLabelAttribute('icon', 'envelope open outline')
        ;
    }
}
