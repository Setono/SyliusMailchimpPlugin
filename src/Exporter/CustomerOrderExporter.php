<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Exporter;

use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Exception\NotSetUpException;
use Sylius\Component\Core\Model\OrderInterface;

final class CustomerOrderExporter implements CustomerOrderExporterInterface
{
    /** @var MailchimpApiClientInterface */
    private $mailChimpApiClient;

    public function __construct(MailchimpApiClientInterface $mailchimpApiClient)
    {
        $this->mailChimpApiClient = $mailchimpApiClient;
    }

    public function exportOrder(OrderInterface $order): void
    {
        try {
            $this->mailChimpApiClient->exportOrder($order);
        } catch (NotSetUpException $e) {
        }
    }
}
