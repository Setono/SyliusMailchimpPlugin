<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Factory;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class MailchimpListFactory implements MailchimpListFactoryInterface
{
    /** @var FactoryInterface */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): AudienceInterface
    {
        /** @var AudienceInterface $mailchimpList */
        $mailchimpList = $this->factory->createNew();

        return $mailchimpList;
    }

    /**
     * {@inheritdoc}
     */
    public function createForMailchimpConfig(MailchimpConfigInterface $mailchimpConfig): AudienceInterface
    {
        $mailchimpList = $this->createNew();
        $mailchimpList->setConfig($mailchimpConfig);

        return $mailchimpList;
    }
}
