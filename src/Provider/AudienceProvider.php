<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Provider;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;

final class AudienceProvider implements AudienceProviderInterface
{
    /** @var AudienceRepositoryInterface */
    private $audienceRepository;

    public function __construct(AudienceRepositoryInterface $audienceRepository)
    {
        $this->audienceRepository = $audienceRepository;
    }

    public function getAudienceFromOrder(OrderInterface $order): ?AudienceInterface
    {
        $channel = $order->getChannel();
        if (null === $channel) {
            return null;
        }

        return $this->audienceRepository->findOneByChannel($channel);
    }

    public function getAudienceFromCustomerOrders(CustomerInterface $customer): ?AudienceInterface
    {
        $audience = null;
        /** @var OrderInterface $order */
        foreach ($customer->getOrders() as $order) {
            $audience = $this->getAudienceFromOrder($order);

            if (null !== $audience) {
                break;
            }
        }

        return $audience;
    }
}
