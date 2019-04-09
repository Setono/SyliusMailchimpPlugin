<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Menu;

use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Context\MailchimpConfigContextInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Setono\SyliusMailchimpPlugin\Menu\MailchimpConfigMenuBuilder;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class MailchimpConfigMenuBuilderSpec extends ObjectBehavior
{
    function let(MailchimpConfigContextInterface $mailChimpConfigContext): void
    {
        $this->beConstructedWith($mailChimpConfigContext);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(MailchimpConfigMenuBuilder::class);
    }

    function it_builds_menu(
        MenuBuilderEvent $menuBuilderEvent,
        ItemInterface $menu,
        ItemInterface $configurationMenu,
        ItemInterface $mailChimpMenuItem,
        MailchimpConfigContextInterface $mailChimpConfigContext,
        MailchimpConfigInterface $mailChimpConfig
    ): void {
        $mailChimpConfig->getId()->willReturn(1);
        $mailChimpConfigContext->getConfig()->willReturn($mailChimpConfig);
        $menuBuilderEvent->getMenu()->willReturn($menu);
        $menu->getChild('configuration')->willReturn($configurationMenu);
        $configurationMenu->addChild('mailchimp', [
            'route' => 'setono_sylius_mailchimp_admin_config_update',
            'routeParameters' => ['id' => 1],
        ])->willReturn($mailChimpMenuItem);

        $mailChimpMenuItem->setLabel('setono_sylius_mailchimp.ui.config_menu')->willReturn($mailChimpMenuItem);
        $mailChimpMenuItem->setLabelAttribute('icon', 'envelope open outline')->shouldBeCalledOnce();

        $this->addConfigItem($menuBuilderEvent);
    }
}
