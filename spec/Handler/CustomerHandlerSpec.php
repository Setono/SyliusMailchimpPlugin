<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Handler\CustomerHandler;
use Setono\SyliusMailchimpPlugin\Handler\CustomerHandlerInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

final class CustomerHandlerSpec extends ObjectBehavior
{
    public function let(ClientInterface $client, ObjectManager $customerManager): void
    {
        $this->beConstructedWith($client, $customerManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(CustomerHandler::class);
    }

    public function it_implements_customer_handler_interface(): void
    {
        $this->shouldImplement(CustomerHandlerInterface::class);
    }

    public function it_returns_false_if_api_call_went_wrong_during_customer_subscription(
        ClientInterface $client,
        AudienceInterface $audience,
        CustomerInterface $customer
    ): void {
        $client->updateMember($audience, $customer)->willThrow(new \Exception('Super exception'));

        $this->subscribeCustomerToAudience($audience, $customer)->shouldReturn(false);
    }

    public function it_subscribes_customer_to_audience(
        ClientInterface $client,
        AudienceInterface $audience,
        CustomerInterface $customer
    ): void {
        $client->updateMember($audience, $customer)->shouldBeCalled();
        $customer->setPushedToMailchimp(Argument::type(\DateTime::class))->shouldBeCalled();
        $customer->setUpdatedAt(Argument::type(\DateTime::class))->shouldBeCalled();

        $this->subscribeCustomerToAudience($audience, $customer)->shouldReturn(true);
    }
}
