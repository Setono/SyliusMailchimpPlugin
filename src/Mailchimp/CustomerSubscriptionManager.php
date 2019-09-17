<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Exception\CustomerNotExportableException;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientFactoryInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Setono\SyliusMailchimpPlugin\Model\MailchimpListInterface;

final class CustomerSubscriptionManager implements CustomerSubscriptionManagerInterface
{
    /** @var MailchimpApiClientFactoryInterface */
    private $mailchimpApiClientFactory;

    /** @var MergeFieldsGeneratorInterface */
    private $mergeFieldsGenerator;

    public function __construct(
        MailchimpApiClientFactoryInterface $mailchimpApiClientFactory,
        MergeFieldsGeneratorInterface $mergeFieldsGenerator
    ) {
        $this->mailchimpApiClientFactory = $mailchimpApiClientFactory;
        $this->mergeFieldsGenerator = $mergeFieldsGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribeCustomerToList(MailchimpListInterface $mailchimpList, CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): void
    {
        if (!$mailchimpList->isCustomerExportable($customer)) {
            throw new CustomerNotExportableException(sprintf(
                'Customer %s is not exportable',
                $customer->getEmail()
            ));
        }

//        try {
            $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpList->getConfig());

            $mergeFields = $this->mergeFieldsGenerator->generateInitialMergeFields($customer, $channelCode, $localeCode);
            $options = [
                'merge_fields' => $mergeFields

                // @see https://mailchimp.com/help/view-and-edit-contact-languages/
                // 'language' => substr($localeCode, 0, 2)
            ];

            dump($options);

            $apiClient->exportEmail(
                $mailchimpList->getListId(),
                $customer->getEmail(),
                $options
            );
//        } catch (\Exception $e) {
//            // $mailchimpList->addCustomerErrored($customer);
//            return;
//        }

        $mailchimpList->addExportedCustomer($customer);
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribeCustomerFromList(MailchimpListInterface $mailchimpList, CustomerInterface $customer): void
    {
        try {
            $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpList->getConfig());
            $apiClient->removeEmail(
                $mailchimpList->getListId(),
                $customer->getEmailCanonical()
            );

            $mailchimpList->removeExportedCustomer($customer);
        } catch (\Exception $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateCustomersMergeFieldsForList(MailchimpListInterface $mailchimpList, CustomerInterface $customer, ?string $oldCustomerEmail = null): void
    {
        if (!$mailchimpList->isCustomerExportable($customer)) {
            return;
        }

        try {
            $apiClient = $this->mailchimpApiClientFactory->buildClient($mailchimpList->getConfig());

            $options = [
                'merge_fields' => $this->mergeFieldsGenerator->generateUpdateMergeFields($customer),
            ];

            if (null !== $oldCustomerEmail) {
                $apiClient->updateEmail(
                    $mailchimpList->getListId(),
                    $customer->getEmail(),
                    $options,
                    $oldCustomerEmail
                );
            } else {
                $apiClient->exportEmail(
                    $mailchimpList->getListId(),
                    $customer->getEmail(),
                    $options
                );
            }
        } catch (\Exception $e) {
            // @todo ?
            return;
        }
    }
}
