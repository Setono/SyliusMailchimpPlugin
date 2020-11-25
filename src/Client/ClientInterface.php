<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Client;

use Setono\SyliusMailchimpPlugin\Exception\ClientException;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;

interface ClientInterface
{
    public function getAudiences(array $options = []): array;

    /**
     * This will create/update a order within Mailchimp
     *
     * @throws ClientException
     */
    public function updateOrder(OrderInterface $order): void;

    /**
     * This will create/update a store within Mailchimp. It will take the audience id from
     * the audience and associate it with the data in the channel (the store in MC lingo)
     *
     * @throws ClientException
     */
    public function updateStore(AudienceInterface $audience): void;

    /**
     * This will update or create a member
     *
     * @throws ClientException
     */
    public function updateMember(AudienceInterface $audience, CustomerInterface $customer): void;

    /**
     * Will just subscribe an email to the given audience
     * This is typically used in subscribe forms on every page
     * where the user isn't logged in or anything
     *
     * @throws ClientException
     */
    public function subscribeEmail(AudienceInterface $audience, string $email): void;

    /**
     * Will try to ping Mailchimp. If the API key is invalid or something else goes wrong, this will throw an exception
     */
    public function ping(): void;
}
