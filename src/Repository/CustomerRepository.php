<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Repository;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface as SyliusCustomerRepositoryInterface;

final class CustomerRepository implements CustomerRepositoryInterface
{
    /** @var SyliusCustomerRepositoryInterface */
    private $syliusCustomerRepository;

    /** @var MailchimpExportRepositoryInterface */
    private $mailchimpExportRepository;

    public function __construct(
        SyliusCustomerRepositoryInterface $syliusCustomerRepository,
        MailchimpExportRepositoryInterface $mailchimpExportRepository
    ) {
        $this->syliusCustomerRepository = $syliusCustomerRepository;
        $this->mailchimpExportRepository = $mailchimpExportRepository;
    }

    public function findNonExportedCustomers(): array
    {
        $customers = [];

        /** @var CustomerInterface[] $subscribedCustomers */
        $subscribedCustomers = $this->syliusCustomerRepository->findBy(['subscribedToNewsletter' => true]);

        foreach ($subscribedCustomers as $customer) {
            if (false === $this->mailchimpExportRepository->isCustomerExported($customer)) {
                $customers[] = $customer;
            }
        }

        return $customers;
    }

    public function findAll(): array
    {
        $customers = [];

        /** @var CustomerInterface[] $subscribedCustomers */
        $subscribedCustomers = $this->syliusCustomerRepository->findAll();

        foreach ($subscribedCustomers as $customer) {
            if (false === $this->mailchimpExportRepository->isCustomerExported($customer)) {
                $customers[] = $customer;
            }
        }

        return $customers;
    }
}
