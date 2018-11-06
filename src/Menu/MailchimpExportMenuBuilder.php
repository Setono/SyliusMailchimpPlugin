<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class MailchimpExportMenuBuilder
{
    public function addMarketingItem(MenuBuilderEvent $event): void
    {
        $marketingMenu = $event->getMenu()->getChild('marketing');

        $marketingMenu
            ->addChild('mailchimp', ['route' => 'setono_sylius_mailchimp_export_plugin_admin_export_index'])
            ->setLabel('setono_sylius_mailchimp_export_plugin.ui.export_menu')
            ->setLabelAttribute('icon', 'arrow up')
        ;
    }
}
