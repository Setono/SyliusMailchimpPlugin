<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class MailchimpExportMenuBuilder
{
    public function addMarketingItem(MenuBuilderEvent $event): void
    {
        $marketingMenu = $event->getMenu()->getChild('marketing');

        if (null === $marketingMenu) {
            return;
        }

        $marketingMenu
            ->addChild('mailchimp', ['route' => 'setono_sylius_mailchimp_admin_mailchimp_export_index'])
            ->setLabel('setono_sylius_mailchimp.ui.export_menu')
            ->setLabelAttribute('icon', 'arrow up')
        ;
    }
}
