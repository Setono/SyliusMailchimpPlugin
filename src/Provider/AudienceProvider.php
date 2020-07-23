<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Provider;

use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;

final class AudienceProvider implements AudienceProviderInterface
{
    /** @var AudienceRepositoryInterface */
    private $audienceRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(AudienceRepositoryInterface $audienceRepository, ChannelContextInterface $channelContext)
    {
        $this->audienceRepository = $audienceRepository;
        $this->channelContext = $channelContext;
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
        /** @var OrderInterface $order */
        foreach ($customer->getOrders() as $order) {
            $audience = $this->getAudienceFromOrder($order);

            if (null !== $audience) {
                return $audience;
            }
        }

        return null;
    }

    public function getAudienceFromContext(): ?AudienceInterface
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $this->audienceRepository->findOneByChannel($channel);
        } catch (ChannelNotFoundException $exception) {
            return null;
        }
    }
}
