<?php

declare(strict_types=1);

namespace Setono\SyliusMailChimpPlugin\Repository;

use Sylius\Component\Core\Repository\CustomerRepositoryInterface as SyliusCustomerRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CustomerRepository implements CustomerRepositoryInterface
{
    /** @var SyliusCustomerRepositoryInterface */
    private $syliusCustomerRepository;

    /** @var RepositoryInterface */
    private $mailChimpExportRepository;

    public function __construct(
        SyliusCustomerRepositoryInterface $syliusCustomerRepository,
        MailChimpExportRepositoryInterface $mailChimpExportRepository
    ) {
        $this->syliusCustomerRepository = $syliusCustomerRepository;
        $this->mailChimpExportRepository = $mailChimpExportRepository;
    }

    public function findNonExportedCustomers(): array
    {
        $customers = [];
        $subscribedCustomers = $this->syliusCustomerRepository->findBy(['subscribedToNewsletter' => true]);

        foreach ($subscribedCustomers as $customer) {
            if (false === $this->mailChimpExportRepository->isCustomerExported($customer)) {
                $customers[] = $customer;
            }
        }

        return $customers;
    }

    public function findAll(): array
    {
        $customers = [];
        $subscribedCustomers = $this->syliusCustomerRepository->findAll();

        foreach ($subscribedCustomers as $customer) {
            if (false === $this->mailChimpExportRepository->isCustomerExported($customer)) {
                $customers[] = $customer;
            }
        }

        return $customers;
    }
}
