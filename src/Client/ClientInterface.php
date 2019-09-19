<?php
declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Client;

use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

interface ClientInterface
{
    public function getAudiences(array $options = []): array;

    /**
     * This will create/update a store within Mailchimp. It will take the audience id from
     * the audience and associate it with the data in the channel (the store in MC lingo)
     */
    public function updateStore(AudienceInterface $audience): void;

    /**
     * This will update or create a member
     */
    public function updateMember(AudienceInterface $audience, CustomerInterface $customer): void;
}
