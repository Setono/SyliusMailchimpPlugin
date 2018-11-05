<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Menu;

use Setono\SyliusMailChimpPlugin\Context\MailChimpConfigContextInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class MailChimpConfigMenuBuilder
{
    /** @var MailChimpConfigContextInterface */
    private $mailChimpConfigContext;

    public function __construct(MailChimpConfigContextInterface $mailChimpConfigContext)
    {
        $this->mailChimpConfigContext = $mailChimpConfigContext;
    }

    public function addConfigItem(MenuBuilderEvent $event): void
    {
        $configurationMenu = $event->getMenu()->getChild('configuration');

        $configurationMenu
            ->addChild('mailchimp', [
                'route' => 'setono_sylius_mailchimp_export_plugin_admin_config_update',
                'routeParameters' => ['id' => $this->mailChimpConfigContext->getConfig()->getId()],
            ])
            ->setLabel('setono_sylius_mailchimp_export_plugin.ui.config_menu')
            ->setLabelAttribute('icon', 'envelope open outline')
        ;
    }
}
