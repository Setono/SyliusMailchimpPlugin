<?php

declare(strict_types=1);

namespace spec\Setono\SyliusMailchimpPlugin\DataGenerator;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusMailchimpPlugin\DataGenerator\AddressDataGeneratorInterface;
use Setono\SyliusMailchimpPlugin\DataGenerator\CustomerDataGenerator;
use Setono\SyliusMailchimpPlugin\DataGenerator\CustomerDataGeneratorInterface;
use Sylius\Component\Core\Model\CustomerInterface;

class CustomerDataGeneratorSpec extends ObjectBehavior
{
    public function let(AddressDataGeneratorInterface $addressDataGenerator): void
    {
        $this->beConstructedWith($addressDataGenerator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CustomerDataGenerator::class);
    }

    public function it_implements_order_line_data_generator_interface(): void
    {
        $this->shouldImplement(CustomerDataGeneratorInterface::class);
    }

    public function it_generates_customer_data(CustomerInterface $customer, Collection $orders): void
    {
        $customer->isSubscribedToNewsletter()->willReturn(true);
        $customer->getId()->willReturn(1);
        $customer->getEmail()->willReturn('john.doe@example.com');
        $customer->getFirstName()->willReturn('John');
        $customer->getLastName()->willReturn('Doe');
        $customer->getOrders()->willReturn($orders);

        $orders->count()->willReturn(10);

        $data = $this->generate($customer);
        $data->toArray()->shouldEqual([
            'id' => '1',
            'email_address' => 'john.doe@example.com',
            'opt_in_status' => true,
            'company' => null,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'orders_count' => 10,
            'total_spent' => null,
            'address' => null,
        ]);
    }
}
