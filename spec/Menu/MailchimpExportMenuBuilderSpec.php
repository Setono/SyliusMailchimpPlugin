<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Menu;

use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Menu\MailchimpExportMenuBuilder;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class MailchimpExportMenuBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MailchimpExportMenuBuilder::class);
    }

    function it_builds_menu(
        MenuBuilderEvent $menuBuilderEvent,
        ItemInterface $globalMenu,
        ItemInterface $subMenu,
        ItemInterface $mailChimpMenuItem
    ): void {
        $menuBuilderEvent->getMenu()->willReturn($globalMenu);
        $globalMenu->getChild('marketing')->willReturn($subMenu);
        $subMenu
            ->addChild('mailchimp', ['route' => 'setono_sylius_mailchimp_export_plugin_admin_export_index'])
            ->willReturn($mailChimpMenuItem)
        ;
        $mailChimpMenuItem
            ->setLabel('setono_sylius_mailchimp_export_plugin.ui.export_menu')
            ->willReturn($mailChimpMenuItem)
        ;
        $mailChimpMenuItem->setLabelAttribute('icon', 'arrow up')->shouldBeCalled();

        $this->addMarketingItem($menuBuilderEvent);
    }
}
