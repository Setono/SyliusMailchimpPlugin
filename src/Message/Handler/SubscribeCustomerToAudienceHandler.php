<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Message\Handler;

use Doctrine\Common\Persistence\ObjectManager;
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

    public function __invoke(SubscribeCustomerToAudience $message): void
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($message->getCustomerId());
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var AudienceInterface|null $audience */
        $audience = $this->audienceRepository->find($message->getAudienceId());
        Assert::isInstanceOf($audience, AudienceInterface::class);

        $customerEmail = $customer->getEmail();
        Assert::notNull($customerEmail);

        try {
            $this->client->updateMember($audience, $customer);

            // todo use workflow

            $this->customerManager->flush();
        } catch (\Exception $exception) {
            // todo handle this exception
        }
    }
}
