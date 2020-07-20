<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\OrderInterface;
use Setono\SyliusMailchimpPlugin\Provider\AudienceProvider;
use Setono\SyliusMailchimpPlugin\Provider\AudienceProviderInterface;
use Sylius\Component\Core\Model\Channel;
use Tests\Setono\SyliusMailchimpPlugin\Application\Model\Order;

final class AudienceProviderSpec extends ObjectBehavior
{
    public function let(AudienceRepositoryInterface $audienceRepository): void
    {
        $this->beConstructedWith($audienceRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(AudienceProvider::class);
    }

    public function it_implements_audience_provider_interface(): void
    {
        $this->shouldImplement(AudienceProviderInterface::class);
    }

    public function it_does_not_provide_audience_from_order_if_it_has_no_channel(OrderInterface $order): void
    {
        $order->getChannel()->willReturn(null);

        $this->getAudienceFromOrder($order)->shouldReturn(null);
    }

    public function it_provides_audience_from_order(
        AudienceRepositoryInterface $audienceRepository,
        AudienceInterface $audience
    ): void {
        $channel = new Channel();
        $order = new Order();
        $order->setChannel($channel);
        $audienceRepository->findOneByChannel($channel)->willReturn($audience);

        $this->getAudienceFromOrder($order)->shouldReturn($audience);
    }

    public function it_does_not_provide_audience_from_customer_if_it_has_no_order(CustomerInterface $customer): void
    {
        $customer->getOrders()->willReturn(new ArrayCollection());

        $this->getAudienceFromCustomerOrders($customer)->shouldReturn(null);
    }

    public function it_provides_audience_from_customer_orders(
        CustomerInterface $customer,
        AudienceRepositoryInterface $audienceRepository,
        AudienceInterface $audience
    ): void {
        $channel = new Channel();
        $order = new Order();
        $order->setChannel($channel);
        $audienceRepository->findOneByChannel($channel)->willReturn($audience);
        $customer->getOrders()->willReturn(new ArrayCollection([$order]));

        $this->getAudienceFromCustomerOrders($customer)->shouldReturn($audience);
    }

    public function it_provides_first_audience_found_among_orders(
        CustomerInterface $customer,
        AudienceRepositoryInterface $audienceRepository,
        AudienceInterface $audience1,
        AudienceInterface $audience2
    ): void {
        $channel1 = new Channel();
        $order1 = new Order();
        $order1->setChannel($channel1);

        $channel2 = new Channel();
        $order2 = new Order();
        $order2->setChannel($channel2);

        $audienceRepository->findOneByChannel($channel1)->willReturn($audience1);
        $audienceRepository->findOneByChannel($channel2)->willReturn($audience2);
        $customer->getOrders()->willReturn(new ArrayCollection([$order1, $order2]));

        $this->getAudienceFromCustomerOrders($customer)->shouldReturn($audience1);
    }
}
