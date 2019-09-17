<?php

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\Mailchimp;

use Setono\SyliusMailchimpPlugin\Model\CustomerInterface;

interface MergeFieldsGeneratorInterface
{
    public function generateInitialMergeFields(CustomerInterface $customer, ?string $channelCode = null, ?string $localeCode = null): array;

    public function generateUpdateMergeFields(CustomerInterface $customer): array;
}
