<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\Message\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Setono\SyliusMailchimpPlugin\Client\ClientInterface;
use Setono\SyliusMailchimpPlugin\Repository\AudienceRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Repository\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Message\Command\SubscribeCustomerToAudience;
use Setono\SyliusMailchimpPlugin\Message\Handler\SubscribeCustomerToAudienceHandler;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

final class SubscribeCustomerToAudienceHandlerSpec extends ObjectBehavior
{
    public function let(
        CustomerRepositoryInterface $customerRepository,
        AudienceRepositoryInterface $audienceRepository,
        ClientInterface $client,
        ObjectManager $customerManager
    ): void {
        $this->beConstructedWith($customerRepository, $audienceRepository, $client, $customerManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(SubscribeCustomerToAudienceHandler::class);
    }

    public function it_throws_exception_if_no_customer_found(CustomerRepositoryInterface $customerRepository): void
    {
        $customerRepository->find(Argument::any())->willReturn(null);

        $message = new SubscribeCustomerToAudience(8, 15);
        $this
            ->shouldThrow(\Exception::class)
            ->during('__invoke', [$message])
        ;
    }

    public function it_throws_exception_if_no_audience_found(
        CustomerRepositoryInterface $customerRepository,
        AudienceRepositoryInterface $audienceRepository,
        CustomerInterface $customer
    ): void {
        $customerRepository->find(Argument::any())->willReturn($customer);
        $audienceRepository->find(Argument::any())->willReturn(null);

        $message = new SubscribeCustomerToAudience(8, 15);
        $this
            ->shouldThrow(\Exception::class)
            ->during('__invoke', [$message])
        ;
    }

    public function it_throws_exception_if_customer_email_is_null(
        CustomerRepositoryInterface $customerRepository,
        AudienceRepositoryInterface $audienceRepository,
        CustomerInterface $customer,
        AudienceInterface $audience
    ): void {
        $customerRepository->find(Argument::any())->willReturn($customer);
        $audienceRepository->find(Argument::any())->willReturn($audience);
        $customer->getEmail()->willReturn(null);

        $message = new SubscribeCustomerToAudience(8, 15);
        $this
            ->shouldThrow(\Exception::class)
            ->during('__invoke', [$message])
        ;
    }

    public function it_pushes_customer_to_mailchimp(
        CustomerRepositoryInterface $customerRepository,
        AudienceRepositoryInterface $audienceRepository,
        CustomerInterface $customer,
        AudienceInterface $audience,
        ClientInterface $client
    ): void {
        $customerRepository->find(Argument::any())->willReturn($customer);
        $audienceRepository->find(Argument::any())->willReturn($audience);
        $customer->getEmail()->willReturn('test@domain.tld');
        $customer->getFirstName()->willReturn('Test');
        $customer->getLastName()->willReturn('User');
        $customer->setPushedToMailchimp(Argument::any())->shouldBeCalled();
        $customer->setUpdatedAt(Argument::any())->shouldBeCalled();

        $client->updateMember($audience, $customer)->shouldBeCalled();

        $message = new SubscribeCustomerToAudience(8, 15);
        $this->__invoke($message)->shouldReturn(true);
    }

    public function it_pushes_only_email_to_mailchimp(
        CustomerRepositoryInterface $customerRepository,
        AudienceRepositoryInterface $audienceRepository,
        CustomerInterface $customer,
        AudienceInterface $audience,
        ClientInterface $client
    ): void {
        $customerRepository->find(Argument::any())->willReturn($customer);
        $audienceRepository->find(Argument::any())->willReturn($audience);
        $customer->getEmail()->willReturn('test@domain.tld');
        $customer->setPushedToMailchimp(Argument::any())->shouldBeCalled();
        $customer->setUpdatedAt(Argument::any())->shouldBeCalled();

        $client->updateMember($audience, $customer)->shouldBeCalled();

        $message = new SubscribeCustomerToAudience(8, 15, true);
        $this->__invoke($message)->shouldReturn(true);
    }
}
