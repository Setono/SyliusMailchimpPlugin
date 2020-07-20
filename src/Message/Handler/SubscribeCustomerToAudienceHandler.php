<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Safe\DateTime;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\SubscribeCustomerToAudience;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class SubscribeCustomerToAudienceHandler implements MessageHandlerInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var AudienceRepositoryInterface */
    private $audienceRepository;

    /** @var ClientInterface */
    private $client;

    /** @var ObjectManager */
    private $customerManager;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AudienceRepositoryInterface $audienceRepository,
        ClientInterface $client,
        ObjectManager $customerManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->audienceRepository = $audienceRepository;
        $this->client = $client;
        $this->customerManager = $customerManager;
    }

    public function __invoke(SubscribeCustomerToAudience $message): bool
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($message->getCustomerId());
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var AudienceInterface|null $audience */
        $audience = $this->audienceRepository->find($message->getAudienceId());
        Assert::isInstanceOf($audience, AudienceInterface::class);

        $pushEmailOnly = $message->isPushEmailOnly();

        $customerEmail = $customer->getEmail();
        Assert::notNull($customerEmail);

        try {
            $pushEmailOnly
                ? $this->client->subscribeEmail($audience, $customerEmail)
                : $this->client->updateMember($audience, $customer);
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
