<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Menu;

use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class MailchimpConfigMenuBuilder
{
    /** @var MailchimpConfigContextInterface */
    private $mailchimpConfigContext;

    public function __construct(MailchimpConfigContextInterface $mailChimpConfigContext)
    {
        $this->mailchimpConfigContext = $mailChimpConfigContext;
    }

    public function addConfigItem(MenuBuilderEvent $event): void
    {
        $configurationMenu = $event->getMenu()->getChild('configuration');

        if (null === $configurationMenu) {
            return;
        }

        $configurationMenu
            ->addChild('mailchimp', [
                'route' => 'setono_sylius_mailchimp_admin_config_update',
                'routeParameters' => ['id' => $this->mailchimpConfigContext->getConfig()->getId()],
            ])
            ->setLabel('setono_sylius_mailchimp.ui.config_menu')
            ->setLabelAttribute('icon', 'envelope open outline')
        ;
    }
}
