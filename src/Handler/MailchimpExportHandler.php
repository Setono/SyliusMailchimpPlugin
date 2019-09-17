<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Handler;

use Doctrine\ORM\EntityManager;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\CustomerRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Doctrine\ORM\MailchimpExportRepositoryInterface;
use Setono\SyliusMailchimpPlugin\Mailchimp\CustomerSubscriptionManagerInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpExportInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class MailchimpExportHandler implements MailchimpExportHandlerInterface
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var EntityManager */
    private $customerManager;

    /** @var MailchimpExportRepositoryInterface */
    private $mailchimpExportRepository;

    /** @var CustomerSubscriptionManagerInterface */
    private $customerSubscriptionManager;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        EntityManager $customerManager,
        MailchimpExportRepositoryInterface $mailchimpExportRepository,
        CustomerSubscriptionManagerInterface $customerSubscriptionManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerManager = $customerManager;
        $this->mailchimpExportRepository = $mailchimpExportRepository;
        $this->customerSubscriptionManager = $customerSubscriptionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResourceInterface $resource, int $limit): void
    {
        Assert::isInstanceOf($resource, MailchimpExportInterface::class);

        /** @var MailchimpExportInterface $mailchimpExport */
        $mailchimpExport = $resource;

        /** @var MailchimpListInterface $mailchimpList */
        $mailchimpList = $mailchimpExport->getList();

        $customers = $mailchimpList->isExportSubscribedOnly() ?
            $this->customerRepository->findNotExportedSubscribers($mailchimpList, $limit) :
            $this->customerRepository->findAllNotExported($mailchimpList, $limit)
        ;

        $initialErrorsCount = $mailchimpExport->getErrorsCount();
        if (0 == count($customers)) {
            // Once no unexported customers found, count this export as completed
            $mailchimpExport->setState(MailchimpExportInterface::COMPLETED_STATE);
            $mailchimpExport->setFinishedAt(new \DateTime());
        } else {
            /** @var CustomerInterface $customer */
            foreach ($customers as $customer) {
                try {
                    $this->customerSubscriptionManager->subscribeCustomerToList(
                        $mailchimpList,
                        $customer
                    );
                } catch (\Exception $exception) {
                    $mailchimpExport->addError($exception->getMessage());
                    // @todo Add $customer->addErroredToMailchimpList($mailchimpList); on error?
                }

                $mailchimpExport->addCustomer($customer);
                $customer->addExportedToMailchimpList($mailchimpList);
            }
            $this->customerManager->flush();

            if ($mailchimpExport->getErrorsCount() > $initialErrorsCount) {
                $mailchimpExport->setState(MailchimpExportInterface::FAILED_STATE);
                $mailchimpExport->setFinishedAt(new \DateTime());
            } else {
                $mailchimpExport->setState(MailchimpExportInterface::IN_PROGRESS_STATE);
            }
        }

        $this->mailchimpExportRepository->add($mailchimpExport);
    }
}
