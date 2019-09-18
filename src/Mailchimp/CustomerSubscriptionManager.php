<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Exception\CustomerNotExportableException;
use Setono\SyliusMailchimpPlugin\Mailchimp\ApiClient\MailchimpApiClientInterface;
use Setono\SyliusMailchimpPlugin\Model\AudienceInterface;
use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

final class CustomerSubscriptionManager implements CustomerSubscriptionManagerInterface
{
    /** @var MailchimpApiClientInterface */
    private $mailchimpApiClient;

    /** @var MergeFieldsGeneratorInterface */
    private $mergeFieldsGenerator;

    public function __construct(
        MailchimpApiClientInterface $mailchimpApiClient,
        MergeFieldsGeneratorInterface $mergeFieldsGenerator
    ) {
        $this->mailchimpApiClient = $mailchimpApiClient;
        $this->mergeFieldsGenerator = $mergeFieldsGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribeCustomerToList(AudienceInterface $mailchimpList, CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): void
    {
        if (!$mailchimpList->isCustomerExportable($customer)) {
            throw new CustomerNotExportableException(sprintf(
                'Customer %s is not exportable',
                $customer->getEmail()
            ));
        }

//        try {
        $apiClient = $this->mailchimpApiClient;

        $mergeFields = $this->mergeFieldsGenerator->generateInitialMergeFields($customer, $channelCode, $localeCode);
        $options = [
                'merge_fields' => $mergeFields,

                // @see https://mailchimp.com/help/view-and-edit-contact-languages/
                // 'language' => substr($localeCode, 0, 2)
            ];

        dump($options);

        $apiClient->exportEmail(
                $mailchimpList->getAudienceId(),
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
    public function unsubscribeCustomerFromList(AudienceInterface $mailchimpList, CustomerInterface $customer): void
    {
        try {
            $apiClient = $this->mailchimpApiClient->buildClient($mailchimpList->getConfig());
            $apiClient->removeEmail(
                $mailchimpList->getAudienceId(),
                $customer->getEmailCanonical()
            );

            $mailchimpList->removeExportedCustomer($customer);
        } catch (\Exception $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateCustomersMergeFieldsForList(AudienceInterface $mailchimpList, CustomerInterface $customer, ?string $oldCustomerEmail = null): void
    {
        if (!$mailchimpList->isCustomerExportable($customer)) {
            return;
        }

        try {
            $apiClient = $this->mailchimpApiClient->buildClient($mailchimpList->getConfig());

            $options = [
                'merge_fields' => $this->mergeFieldsGenerator->generateUpdateMergeFields($customer),
            ];

            if (null !== $oldCustomerEmail) {
                $apiClient->updateEmail(
                    $mailchimpList->getAudienceId(),
                    $customer->getEmail(),
                    $options,
                    $oldCustomerEmail
                );
            } else {
                $apiClient->exportEmail(
                    $mailchimpList->getAudienceId(),
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
