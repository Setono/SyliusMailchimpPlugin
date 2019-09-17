<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;
use Sylius\Component\Core\Model\AddressInterface;

final class MergeFieldsGenerator implements MergeFieldsGeneratorInterface
{
    /** @var array */
    private $mailchimpMergeFields;

    public function __construct(array $mailchimpMergeFields)
    {
        $this->mailchimpMergeFields = $mailchimpMergeFields;
    }

    public function generateInitialMergeFields(CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): array
    {
        $mergeFields = $this->generateUpdateMergeFields($customer);

        if ($this->mailchimpMergeFields['channel'] && null !== $channelCode) {
            $channelCode = $customer->getLastOrderChannelCode($channelCode);
            if ($channelCode) {
                $mergeFields[$this->mailchimpMergeFields['channel']] = $channelCode;
            }
        }

        if ($this->mailchimpMergeFields['locale'] && null !== $localeCode) {
            $localeCode = $customer->getLastOrderLocaleCode($localeCode);
            if ($localeCode) {
                $mergeFields[$this->mailchimpMergeFields['locale']] = $localeCode;
            }
        }

        return $mergeFields;
    }

    public function generateUpdateMergeFields(CustomerInterface $customer): array
    {
        $mergeFields = [
            // This mergefield's name non-configurable/hardcoded
            'EMAIL' => $customer->getEmailCanonical(),
        ];

        if ($this->mailchimpMergeFields['first_name'] && $customer->getFirstName()) {
            $mergeFields[$this->mailchimpMergeFields['first_name']] = $customer->getFirstName();
        }

        if ($this->mailchimpMergeFields['last_name'] && $customer->getLastName()) {
            $mergeFields[$this->mailchimpMergeFields['last_name']] = $customer->getLastName();
        }

        if ($this->mailchimpMergeFields['address']) {
            $address = $this->getCustomerAddress($customer);

            if ($address instanceof AddressInterface) {
                $mergeFields[$this->mailchimpMergeFields['address']] = sprintf(
                    '%s, %s, %s, %s',
                    $address->getCountryCode(),
                    $address->getProvinceName(),
                    $address->getCity(),
                    $address->getStreet()
                );
            }
        }

        if ($this->mailchimpMergeFields['phone']) {
            $phone = $this->getCustomerPhone($customer);
            if (null !== $phone) {
                $mergeFields[$this->mailchimpMergeFields['phone']] = $phone;
            }
        }

        return $mergeFields;
    }

    private function getCustomerAddress(CustomerInterface $customer): ?AddressInterface
    {
        if (null !== $customer->getDefaultAddress()) {
            return $customer->getDefaultAddress();
        }

        if (!$customer->getAddresses()->isEmpty()) {
            return $customer->getAddresses()->first();
        }

        return null;
    }

    private function getCustomerPhone(CustomerInterface $customer): ?string
    {
        if (null !== $customer->getPhoneNumber()) {
            return $customer->getPhoneNumber();
        }

        $address = $this->getCustomerAddress($customer);
        if ($address instanceof AddressInterface) {
            return $address->getPhoneNumber();
        }

        return null;
    }
}
