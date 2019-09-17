<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Factory;

use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class MailchimpExportFactory implements MailchimpExportFactoryInterface
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
    public function createNew(): MailchimpExportInterface
    {
        /** @var MailchimpExportInterface $mailchimpExport */
        $mailchimpExport = $this->factory->createNew();

        return $mailchimpExport;
    }

    /**
     * {@inheritdoc}
     */
    public function createForMailchimpList(MailchimpListInterface $mailchimpList): MailchimpExportInterface
    {
        $mailchimpExport = $this->createNew();
        $mailchimpExport->setList($mailchimpList);

        return $mailchimpExport;
    }
}
