<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class MailchimpConfigFormMenuBuilder
{
    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!array_key_exists('config', $options) || !$options['config'] instanceof MailchimpConfigInterface) {
            return $menu;
        }

        $menu
            ->addChild('details')
            ->setAttribute('template', '@SetonoSyliusMailchimpPlugin/Admin/Mailchimp/Config/Tab/_details.html.twig')
            ->setLabel('sylius.ui.details')
            ->setCurrent(true)
        ;

        // @todo Add lists tab when proper way will be found
//        $menu
//            ->addChild('lists')
//            ->setAttribute('template', '@SetonoSyliusMailchimpPlugin/Admin/Mailchimp/Config/Tab/_lists.html.twig')
//            ->setLabel('setono_sylius_mailchimp.ui.lists')
//        ;

        return $menu;
    }
}
