<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Safe\DateTime;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

final class CustomerHandler implements CustomerHandlerInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var ObjectManager */
    private $customerManager;

    public function __construct(ClientInterface $client, ObjectManager $customerManager)
    {
        $this->client = $client;
        $this->customerManager = $customerManager;
    }

    public function subscribeCustomerToAudience(AudienceInterface $audience, CustomerInterface $customer): bool
    {
        try {
            $this->client->updateMember($audience, $customer);
        } catch (\Exception $exception) {
            return false;
        }

        $now = new DateTime();
        $customer->setPushedToMailchimp($now);

        // update the updated at manually so that we are sure
        // it will be the same value as the pushed to mailchimp value
        $customer->setUpdatedAt($now);

        $this->customerManager->flush();

        return true;
    }
}
