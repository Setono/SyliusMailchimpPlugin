<?php


namespace Setono\SyliusMailchimpPlugin\Exporter;

use Setono\SyliusMailchimpPlugin\ApiClient\MailchimpApiClientInterface;
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
        $this->mailChimpApiClient->exportOrder($order);
    }
}
