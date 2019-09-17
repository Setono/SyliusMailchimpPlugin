<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient;

use DrewM\MailChimp\MailChimp as Client;
use Setono\SyliusMailchimpPlugin\Model\MailchimpConfigInterface;

final class MailchimpApiClientFactory implements MailchimpApiClientFactoryInterface
{
    /** @var MailchimpApiClientInterface[] */
    private $clients;

    /**
     * {@inheritdoc}
     */
    public function buildClient(MailchimpConfigInterface $mailchimpConfig): MailchimpApiClientInterface
    {
        if (!isset($this->clients[$mailchimpConfig->getId()])) {
            $this->clients[$mailchimpConfig->getId()] = new MailchimpApiClient(
                new Client($mailchimpConfig->getApiKey())
            );
        }

        return $this->clients[$mailchimpConfig->getId()];
    }
}
